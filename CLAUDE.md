# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Volontariat is a Symfony 7.3 application that serves as a volunteering platform connecting volunteers (volontaires) with associations. It's a French-language platform ("Plate-forme du volontariat - Un lieu virtuel de rencontre entre volontaires et associations pour des implications concrètes").

## Common Commands

```bash
# Install dependencies
composer install

# Clear cache
php bin/console cache:clear

# Run database migrations
php bin/console doctrine:migrations:migrate

# Run tests
php bin/console --env=test && vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/YourTestFile.php

# Build Tailwind CSS
php bin/console tailwind:build

# Watch Tailwind CSS during development
php bin/console tailwind:build --watch

# Install/update asset map
php bin/console importmap:install
```

## Architecture

### Dual Namespace Structure

The application uses two namespaces configured in `composer.json`:

- `App\` → `src/App` - Contains only the Kernel
- `AcMarche\Volontariat\` → `src/AcMarche/Volontariat/src` - Contains all business logic

The main application code lives in `AcMarcheVolontariatBundle`, a reusable Symfony bundle registered in `config/bundles.php`.

### Bundle Self-Configuration

The bundle (`AcMarcheVolontariatBundle`) loads its own configuration in `prependExtension()`:
- Doctrine entity mappings
- Security configuration
- Vich Uploader settings
- Liip Imagine settings
- Rate limiter configuration
- Twig configuration
- Routing

### Key Directories (under `src/AcMarche/Volontariat/src/`)

- `Controller/` - Public-facing controllers
- `Controller/Admin/` - Admin panel controllers (ROLE_ADMIN)
- `Controller/Backend/` - Association management controllers (ROLE_ASSOCIATION)
- `Entity/` - Doctrine entities with PHP 8 attributes
- `Entity/Security/` - User and Token entities
- `Repository/` - Doctrine repositories
- `Form/` - Symfony form types
- `Mailer/` - Email handling
- `Security/` - Authentication/authorization logic
- `Service/` - Business logic services
- `Twig/` - Twig extensions
- `Validator/` - Custom validation constraints

### Templates Location

Templates are in `src/AcMarche/Volontariat/templates/` (not the root `templates/` directory).

### Core Domain Entities

- `Volontaire` - Volunteer profile with personal info, secteurs, availability
- `Association` - Organization profile with contact info, secteurs, besoins
- `Secteur` - Activity sector categories
- `Besoin` - Volunteer needs posted by associations
- `User` - Authentication entity linked to Volontaire or Association

### Entity Traits and Behaviors

Entities use Knp DoctrineBehaviors for:
- `SluggableTrait` - URL-friendly slugs
- `TimestampableTrait` - Created/updated timestamps
- `UuidTrait` - UUID generation

Vich Uploader handles image uploads with `@Vich\Uploadable` annotation.

### Frontend Stack

- Symfony AssetMapper (no Webpack/Encore)
- Tailwind CSS via SymfonycastsTailwindBundle
- Stimulus + Turbo (Hotwire)
- Bootstrap 5 (for admin)
- Two entrypoints: `app.js` (public) and `app_admin.js` (admin)

### Configuration Files

Bundle-specific config is in PHP format at `src/AcMarche/Volontariat/config/packages/`.
Global app config is in YAML at `config/packages/`.

## Database

MySQL/MariaDB with Doctrine ORM. The bundle maps entities from `AcMarche\Volontariat` namespace separately from `App\Entity`.
