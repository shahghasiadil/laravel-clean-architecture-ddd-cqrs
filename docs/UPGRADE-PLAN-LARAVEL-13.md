# Laravel 13 Upgrade & Architecture Hardening Plan

Status: **proposed** — nothing in this document has been implemented yet.
Target branch: `claude/laravel-13-upgrade-plan-r88cxx`
Audit date: 2026-08-16, against commit `2fccb7e`.

---

## 1. Where the template stands today

Verified facts about the current codebase (not assumptions):

| Item | Current | Notes |
| --- | --- | --- |
| PHP floor | `^8.2` | `composer.json` |
| Framework | `laravel/framework: ^12.0` | Laravel 13.25.0 is current on Packagist |
| Sanctum | `^4.0` | 4.3.3 is the first release declaring `^13.0` |
| Tinker | `^2.9` | 3.0.x is the L13-compatible line |
| Larastan | `^3.0` | 3.9.6+ declares `^13` |
| Pest | `^3.7` | Pest 5 requires PHP `^8.4` + PHPUnit `^13.3` |
| spatie/laravel-data | `^4.13` | 4.22+ declares `illuminate/contracts: ^13.0` |
| PHPStan level | 5, `src/` only | `phpstan.neon` |
| Coverage source | `app/` | `phpunit.xml` — `app/` holds one near-empty provider |
| CI | single job, PHP 8.3, `php artisan test` | no Pint / PHPStan / Rector / Pest arch gate |
| `src/Shared` | **does not exist on disk** | but is PSR-4 autoloaded and arch-tested |

The layering is sound in outline (`Domain` / `Application` / `Infrastructure` /
`Presentation`, one provider per layer, Pest arch tests enforcing direction). The
problems are in the details, and they are the kind that a template propagates
into every project generated from it.

---

## 2. Decision required before Phase 1

**PHP floor: 8.3 or 8.4?**

Laravel 13 requires PHP `^8.3`. Pest 5 requires PHP `^8.4`. These are the only two
coherent targets:

- **Option A — PHP `^8.4`, Pest `^5.0` (recommended).** This is a *template*, not a
  legacy app; nobody is dragging a PHP 8.3 production box behind them. Pest 5 brings
  PHPUnit 13 and a maintained mutation-testing plugin, both of which Phase 7 depends on.
  Cost: the README's advertised support matrix drops 8.2 and 8.3.
- **Option B — PHP `^8.3`, Pest `^4.x`.** Widest compatibility, matches Laravel 13's own
  floor exactly. Cost: Phase 7's mutation-testing gate has to be scaled back or pinned to
  the Pest 4 plugin line.

The rest of this plan assumes **Option A**. If you prefer B, only §3.1 and §9 change.

---

## 3. Phase 1 — Laravel 13 upgrade

Laravel 12 → 13 carries no breaking application-code changes; the work is dependency
resolution and re-establishing the CI floor.

### 3.1 `composer.json`

```jsonc
"require": {
    "php": "^8.4",
    "laravel/framework": "^13.0",
    "laravel/sanctum": "^4.3.3",
    "laravel/tinker": "^3.0",
    "spatie/laravel-data": "^4.23"
},
"require-dev": {
    "fakerphp/faker": "^1.24",
    "larastan/larastan": "^3.10",
    "laravel/pint": "^1.30",
    "laravel/sail": "^1.46",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.9",
    "pestphp/pest": "^5.0",
    "pestphp/pest-plugin-laravel": "^5.0",
    "rector/rector": "^2.6",
    "spatie/laravel-ignition": "^2.9"
}
```

Also fix the stray indentation on the `pestphp/pest` line (it is currently indented with
seven spaces, which is why it visually breaks the block).

### 3.2 Verification checklist

- `laravel/sanctum` no longer auto-loads its migrations. This repo **already** carries
  `database/migrations/2024_05_23_183818_create_personal_access_tokens_table.php`, so no
  action is needed — but confirm no duplicate-table error on a clean `migrate:fresh`.
- `rector.php` uses `->withPhpSets()`, which follows the `composer.json` PHP floor. After
  the bump it will start proposing 8.3/8.4 syntax. Run it and review the diff deliberately
  rather than accepting it wholesale.
- `nesbot/carbon` moves to `^3.8`; `UsersListData` type-hints `Carbon\Carbon` directly.
  Confirm `spatie/laravel-data`'s date casting still resolves it (it should — Carbon 3
  keeps the class name).
- `symfony/*` moves to `^7.4 || ^8.0`.

### 3.3 CI (`.github/workflows/laravel.yml`)

Today CI runs one PHP version and only `php artisan test`. Replace with:

- a matrix over PHP `8.4` and `8.5` (Laravel 13 supports both),
- `actions/checkout@v5`, keep the SHA-pinned `setup-php`,
- Composer dependency caching keyed on `composer.lock`,
- a `concurrency` group so superseded pushes cancel,
- separate gate steps: `pint --test`, `phpstan analyse`, `rector --dry-run`,
  `pest --parallel`, and the architecture suite.

### 3.4 Docs

`README.md:46` advertises PHP `[8.2, 8.3, 8.4]`. Update to match the chosen floor, and
update every Laravel 12 reference.

**Exit criterion for Phase 1:** green CI on the new matrix with the *existing* test suite.
Do not start Phase 2 before this lands — it is the only clean bisect point.

---

## 4. Phase 2 — Fix the layering violations

These are correctness problems in the architecture itself. They matter more than the
version bump.

### 4.1 The Domain depends on the Application layer (inverted)

`src/Domain/User/Repositories/UserRepositoryContract.php` imports
`Application\User\Data\UserData`:

```php
public function save(UserData $data): string;
public function update(int $id, UserData $data): bool;
```

The innermost layer is importing from an outer one. Worse, the arch test at
`tests/Architecture/GlobalsTest.php` *encodes this as legal*:

```php
arch('Application should only accessed by Presentation, Infrastructure and Domain Layer')
    ->expect('Application')
    ->toOnlyBeUsedIn(['Presentation', 'Infrastructure', 'Domain']);
```

**Fix:** the Domain owns its own input types. Introduce
`Domain\User\ValueObjects\{UserId, EmailAddress, UserName, HashedPassword}` and have the
repository contract speak in the aggregate and value objects:

```php
interface UserRepositoryContract
{
    public function save(User $user): UserId;
    public function findByEmail(EmailAddress $email): ?User;
    public function findById(UserId $id): ?User;
    /** @return LazyCollection<int, User> */
    public function paginate(int $perPage, int $page): Paginated;
}
```

Translation from `UserData` (an Application-layer DTO) to the aggregate happens in the
command handler, which is exactly where it belongs. Then tighten the arch rule to
`->toOnlyBeUsedIn(['Presentation', 'Infrastructure'])` so the violation can never return.

### 4.2 The Domain is coupled to Eloquent

`Domain\User\Entities\User` extends `Illuminate\Foundation\Auth\User`, carries
`$fillable` / `$hidden` / `casts()`, and is decorated with `#[ObservedBy]`. It is an
Eloquent model living in the Domain folder — the folder name is the only thing making it
an "entity". `UserRepositoryContract` also returns `Illuminate\Database\Eloquent\Collection`,
so persistence types leak all the way to the Domain boundary.

**Fix — split model from aggregate:**

- `Infrastructure\User\Persistence\Models\UserModel` — the Eloquent model, table mapping,
  casts, observers. Moves out of `Domain` entirely.
- `Domain\User\Entities\User` — a plain PHP aggregate root with value-object properties,
  private constructor, and named constructors (`User::register(...)`,
  `User::reconstitute(...)`) that enforce invariants.
- `Infrastructure\User\Persistence\Mappers\UserMapper` — bidirectional mapping between
  the two.

This is the single highest-value change in the plan. It is what makes the template
actually demonstrate DDD rather than demonstrate folder naming. It is also the most
invasive, so it gets its own PR.

### 4.3 `src/Shared` is autoloaded but absent

`composer.json` maps `Shared\` → `src/Shared/`, `tests/Architecture/GlobalsTest.php`
asserts rules about it, and the directory does not exist. The arch expectations against
an empty namespace pass vacuously.

**Fix:** create `src/Shared/` and give it real contents — see §6.1 — or drop the PSR-4
entry and the arch rules. Half-declared is the worst of the three states.

### 4.4 Typo: `src/Domain/User/Polices/`

Should be `Policies/`. It is empty except for `.gitkeep`, so this is a free rename — but a
template's directory names get copied into every downstream project.

---

## 5. Phase 3 — Rebuild the CQRS core

### 5.1 Marker classes should be interfaces

`Application\Bus\{Command, Query, CommandHandler, QueryHandler}` are empty *concrete
classes* used via `extends`. That burns the single inheritance slot and blocks
`final readonly class CreateUserCommand` composition. Convert all four to interfaces
(`CommandContract`, `QueryContract`, …) and have messages `implements` them.

### 5.2 Queries run through the command bus

`IlluminateQueryBus` injects `Illuminate\Bus\Dispatcher` — the same dispatcher
`IlluminateCommandBus` uses. Queries therefore inherit queueability and command
middleware, which defeats the point of separating them. Give the query bus its own
resolver with no queue path, and type it so a query can never be dispatched
asynchronously.

### 5.3 Handler registration does not scale

`ApplicationServiceProvider::boot()` maps every handler by hand and reaches for the
`app()` helper (service location inside a provider):

```php
$commandBus = app(CommandBusContract::class);
$commandBus->register([CreateUserCommand::class => CreateUserCommandHandler::class]);
```

Every new use case means editing a central file — the classic template bottleneck.

**Fix:** attribute-driven discovery.

```php
#[AsCommandHandler(CreateUserCommand::class)]
final readonly class CreateUserCommandHandler { /* ... */ }
```

A `HandlerRegistry` scans `src/*/Application/**` via `symfony/finder` + reflection,
builds the map once, and caches it to `bootstrap/cache/handlers.php` behind a new
`artisan bus:cache` command wired into `optimize`. Scanning only happens when the cache
is cold, so production pays nothing.

### 5.4 No bus middleware pipeline

There is nowhere to put cross-cutting concerns. Add an `Illuminate\Pipeline`-based
middleware stack on both buses, shipping with:

- `TransactionalMiddleware` — wraps command handling in a DB transaction (commands only).
- `ValidatesCommandMiddleware` — validates the command's own invariants before handling.
- `LogsExecutionMiddleware` — structured log line with message class + duration.
- `DispatchesDomainEventsMiddleware` — flushes recorded domain events *after* commit.
- `IdempotencyMiddleware` (opt-in) — dedupes commands carrying an idempotency key.

### 5.5 The buses are untyped

`dispatch(Command $c): mixed` and `ask(Query $q): mixed` mean PHPStan can infer nothing at
any call site — a real obstacle to the level-8+ target in Phase 7. Add generics:

```php
/**
 * @template TResult
 * @param QueryContract<TResult> $query
 * @return TResult
 */
public function ask(QueryContract $query): mixed;
```

with `@implements QueryContract<UsersListData>` on each concrete query.

### 5.6 `UserService` bypasses CQRS entirely

`UserController` injects `UserServiceContract` alongside the two buses and calls
`index()` / `update()` directly. Two of four endpoints route around the very pattern the
template exists to demonstrate. `UserServiceContract::update($id, UserData $userData)`
also has an untyped `$id`.

**Fix:** delete `UserService` and `UserServiceContract`. Replace with
`UpdateUserCommand` + handler and `GetUsersQuery` + handler. Every controller action then
goes through exactly one bus.

### 5.7 Domain events are absent

`src/Domain/User/Events/.gitkeep` is the whole story. Add an `AggregateRoot` base that
records events (`recordThat()` / `releaseEvents()`), a `UserRegistered` event, and let
`DispatchesDomainEventsMiddleware` publish them after commit. This also gives
`UserObserver` — currently five empty methods — something real to be replaced by.

---

## 6. Phase 4 — Domain building blocks

### 6.1 `src/Shared/`

Populate the namespace that §4.3 leaves dangling:

- `Shared/Domain/ValueObject/{ValueObject, StringValueObject, Uuid}` base types.
- `Shared/Domain/AggregateRoot` and `Shared/Domain/DomainEvent`.
- `Shared/Domain/Exceptions/DomainException` — the root of the domain error hierarchy.
- `Shared/Criteria/{Criteria, Filter, Order}` — a persistence-agnostic query spec so
  repository contracts stop growing a `findByX` method per field.

### 6.2 Typed failures instead of stray Eloquent exceptions

`Domain\User\Exceptions\UserNotFoundException` exists and is **never thrown anywhere**.
Meanwhile `UserRepository::update()` calls `User::findOrFail($id)`, so a missing user
surfaces as Eloquent's `ModelNotFoundException` thrown *from the Infrastructure layer* —
a framework exception crossing the Domain boundary.

**Fix:** Infrastructure catches Eloquent failures and rethrows the Domain exception.
`bootstrap/app.php`'s `withExceptions(...)` closure — currently empty — maps the domain
hierarchy to RFC 9457 problem-details JSON responses:

| Exception | Status |
| --- | --- |
| `EntityNotFoundException` | 404 |
| `DomainValidationException` | 422 |
| `AuthorizationException` | 403 |
| `ConcurrencyException` | 409 |

Right now a `UserNotFoundException` would render as a 500.

### 6.3 Read models

Queries currently return the same Eloquent aggregate the write side uses, which is CQRS in
name only. Introduce `Application\User\ReadModels\UserReadModel` populated by a projector,
plus optional `DB_READ_HOST` / `DB_WRITE_HOST` split in `config/database.php` to show the
pattern honestly. Keep it opt-in — a template shouldn't force eventual consistency on
someone building a CRUD app.

---

## 7. Phase 5 — Presentation layer fixes

Concrete bugs, each with a regression test:

1. **Duplicate emails 500 instead of 422.** `UserFormRequest::rules()` has
   `'email' => ['required', 'email']` with no `unique:users` rule. The DB unique index
   catches it and the app returns a 500. Split into `StoreUserRequest`
   (`unique:users,email`) and `UpdateUserRequest` (`unique:users,email,{id}`).
2. **`password` is `required` on update.** The same shared `UserFormRequest` backs both
   `store` and `update`, so a caller cannot change a name without resubmitting a password.
   `UserData::$password` is typed `string|Optional` specifically to allow this, and the
   request rules prevent it. Make it `sometimes` on update.
3. **`update()` re-queries by email.**
   `UserController::update()` ends with `$this->queryBus->ask(new GetUserByEmailQuery($request->email))`
   — it should look up by the `$id` it just wrote. Under a concurrent email change this
   returns the wrong record or null.
4. **Inconsistent response envelope.** `index()` returns
   `Illuminate\Support\Collection` (serialises to a bare array — see the `'*' => [...]`
   assertion in `tests/Feature/UserTest.php`) while `store`/`show`/`update` return
   `UsersListData` (serialises to `{"data": ...}`). Standardise on a wrapped envelope.
5. **`getAllUsers()` has no pagination.** `User::all()` on the users table. Replace with a
   paginated query object; add `per_page` validation capped at 100.
6. **`authorize()` returns `true`.** Add `UserPolicy` in `Domain/User/Policies/` and wire
   real checks; the empty `Polices/` directory was clearly intended for this.
7. **No rate limiting or versioning.** Move routes under `/api/v1`, apply
   `throttle:api`, and register a per-module route loader so each bounded context
   contributes its own routes instead of `bootstrap/app.php` hard-coding
   `src/Presentation/UserManagement/routes/*`.
8. **`save()` returns an email string as an identifier.**
   `UserRepository::save(): string` returns `$user->email`, and `UserController::store()`
   uses it to re-fetch. Identity should be `UserId`. Also drop the redundant
   `return $user ?? null;` in `findByEmail`/`findById`.

---

## 8. Phase 6 — Developer experience

What makes this a *template* rather than a sample app:

- **Generators.** `artisan make:module Billing` scaffolds the full four-layer slice with
  provider, routes, arch-test entries and a first use case. Plus `make:command-handler`,
  `make:query-handler`, `make:aggregate`, `make:value-object`, `make:domain-event`, each
  with stubs under `stubs/ddd/`. This is the single biggest adoption lever — right now a
  new bounded context is ~15 files of hand-copying.
- **Composer scripts:** `composer test`, `composer lint`, `composer analyse`,
  `composer refactor`, `composer check` (all gates, matching CI exactly).
- **Docs:** an architecture decision record directory (`docs/adr/`), a request-lifecycle
  diagram tracing HTTP → controller → bus → middleware → handler → repository → mapper →
  DB, and a "how to add a bounded context" walkthrough.
- **Observability:** correlation-ID middleware propagated into bus middleware log context;
  optional Pulse/Telescope wiring.
- **`laravel/boost`** as a dev dependency so downstream AI-assisted work has framework
  context.

---

## 9. Phase 7 — Raise the quality bar

- **`phpunit.xml` coverage is measuring the wrong directory.** `<source><include>` points
  at `app/`, which contains only a near-empty `AppServiceProvider`. All real code is in
  `src/`. Every coverage number this repo has ever produced is meaningless. Point it at
  `src/` and add a `<coverage>` minimum threshold.
- **PHPStan 5 → 8, then 10.** Widen `paths` to `app/`, `database/`, `tests/`. Generate a
  baseline at the level you land on, and add a CI step that fails if the baseline grows.
- **Mutation testing** via `pest --mutate` on `src/Domain` and `src/Application`, with a
  minimum MSI — the layers where mutation score actually means something.
- **Tighten the arch tests.** Beyond the §4.1 fix: assert Domain does not touch
  `Illuminate\*` at all, that commands are `final readonly`, that handlers are `final`,
  that value objects are immutable, and that nothing outside `Infrastructure` references
  Eloquent.
- **Real unit tests.** The suite today is 3 feature tests plus arch rules; there is not one
  unit test for a handler, the bus, or the repository. Add handler tests with a fake
  repository and bus-middleware tests.
- **Pint and Rector in CI** — both are configured, neither runs on PRs.

---

## 10. Suggested sequencing

Each row is one PR. Phases 2–4 are ordered by dependency; 5–7 can be parallelised.

| # | PR | Depends on | Risk |
| --- | --- | --- | --- |
| 1 | Laravel 13 + dependency bump, CI matrix, README | — | Low |
| 2 | Fix `phpunit.xml` coverage path, composer scripts, CI gates | 1 | Low |
| 3 | `src/Shared` building blocks; `Polices` → `Policies` | 2 | Low |
| 4 | CQRS core: interfaces, typed buses, split query bus | 3 | Medium |
| 5 | Bus middleware pipeline + attribute handler discovery | 4 | Medium |
| 6 | Domain purity: aggregate/model split, mappers, VOs | 5 | **High** |
| 7 | Domain events + outbox; retire `UserObserver` | 6 | Medium |
| 8 | Retire `UserService`; all endpoints through the buses | 5 | Medium |
| 9 | Presentation fixes §7.1–§7.8 + regression tests | 8 | Medium |
| 10 | Exception → problem-details mapping | 6 | Low |
| 11 | Generators, stubs, `make:module` | 6 | Low |
| 12 | PHPStan 8→10, mutation testing, tightened arch rules | all | Medium |

PR 1 is independently shippable and worth landing on its own — it is the part with a
hard external deadline (Laravel 12 security support ends Q1 2027).

---

## 11. Explicitly out of scope

- Multi-tenancy, event sourcing with a real event store, and message-broker transport
  (Kafka/RabbitMQ). All three are reasonable next steps, but each doubles the template's
  conceptual surface. The outbox table in Phase 4 is the seam to add them later.
- Frontend. `vite.config.js` and `package.json` stay as-is; this is an API template.

---

## 12. Sources

- [Laravel 13 release announcement](https://laravel-news.com/laravel-13-released)
- [Laravel 12 → 13 upgrade notes](https://hafiz.dev/blog/laravel-12-to-13-upgrade-guide)
- Package constraints verified directly against the
  [Packagist v2 metadata API](https://repo.packagist.org/) on 2026-08-16.
