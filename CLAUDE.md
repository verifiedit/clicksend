# CLAUDE.md

Guidance for Claude Code when working in this repository. This file is self-contained — do not rely on `README.md` for build commands.

## Project

ClickSend notification channel for Laravel — published as `verifiedit/laravel-notification-channel-clicksend` and consumed by Verified Laravel apps (e.g. `capture-api`).

## Stack

- **Language:** PHP `>=8.3`
- **Package manager:** Composer
- **Laravel compatibility:** Illuminate 10 / 11 / 12 / 13
- **SDK dependency:** `verifiedit/clicksend-sms ^2.0` (sibling package)
- **Tests:** PHPUnit `^12.5`
- **Style:** Laravel Pint (`pint.json`)
- **Static analysis:** none configured

## Commands

This is a library — no app to boot. Run tooling directly on the host:

```shell
composer install
composer run test                     # PHPUnit (with deprecations + notices)
composer run test:coverage            # PHPUnit + clover coverage
composer run pint                     # auto-fix
composer run pint:test                # check only
```

There are no `phpstan`, hooks, or CI-only scripts defined here.

## Before declaring a task complete

Run, in order: `composer run pint:test`, `composer run test`. Fix every failure.

## Layout

```
src/                                 # NotificationChannels\ClickSend\* — channel + service provider
tests/                               # PHPUnit tests (Tests\NotificationChannels\ClickSend\*)
config/                              # Default package config (publishable)
phpunit.xml.dist                     # PHPUnit config
phpunit.coverage.xml                 # Coverage variant
pint.json                            # Pint preset
CHANGELOG.md                         # Hand-maintained release notes
```

## Conventions

- **Commits / PRs:** match the conventional-style prefix used by recent merges (`feat:`, `fix:`, `chore:`); tag releases with semver (`vX.Y.Z`).
- **PR reviewers:** `verifiedit/dev` (no `CODEOWNERS`).
- **Public API stability.** This package is consumed by other repos — breaking signatures requires a major version bump and coordinated downstream PRs.
- **Framework-light.** Inject `Illuminate\Contracts\*` rather than reaching for facades. The package must keep its broad Laravel range.
- **Type everything** on public methods.
- **Tests are mandatory** for every public method.

## Releasing

- Tag with semver (`vX.Y.Z`). Update `CHANGELOG.md`.
- Downstream apps pin via `composer.json` ranges — do not push downstream PRs from this repo; that is the consumer's responsibility.

## Things to avoid

- Bumping the PHP minimum or dropping a Laravel major without confirming with consumers.
- Hard-coupling to a specific Laravel version inside the channel logic.
- Committing `vendor/` or `.phpunit.result.cache`.

## Skills

- `/code-review`, `/review`, `/security-review`, `/simplify`, `/init`.
