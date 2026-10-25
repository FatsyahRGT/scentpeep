# Graph Report - scentpeep-management-system  (2026-10-17)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 507 nodes · 837 edges · 55 communities (10 shown, 45 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Authentication & Routing
- Composer & PHP Dependencies
- Dashboard & Stock Models
- Database Migrations
- Form Requests & Stock Controllers
- User & Stock Movement Entities
- Frontend Package Config
- Database Seeders & Initial Data
- Database & Cache Configuration
- Application Bootstrap & Exceptions
- Service Providers
- Logging Configuration
- Console Artisan Commands
- Unit & Feature Tests
- Artisan CLI Binary
- Docker Entrypoint Automation
- App Configuration
- Filesystem Configuration
- Mail Configuration
- Queue Configuration
- Services Configuration
- Auth Views
- Master Layout View
- Product Edit View
- Product Index View
- Location Create View
- Location Edit View
- Location Index View
- Stock History Ledger View
- Stock Matrix View
- Stock Movement Form View
- Stock Transfer Form View
- Welcome Landing View
- products/create.blade.php
- products/edit.blade.php
- products/index.blade.php
- stocks/index.blade.php
- transfer.blade.php
- transfers_index.blade.php
- transactions/create.blade.php
- transactions/edit.blade.php
- transactions/index.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 38 edges
2. `Location` - 36 edges
3. `Transaction` - 27 edges
4. `Product` - 26 edges
5. `TransactionAndStockTest` - 15 edges
6. `Stock` - 15 edges
7. `UserController` - 12 edges
8. `ExportReportsTest` - 11 edges
9. `SalesAndCriteriaTest` - 11 edges
10. `TransactionController` - 11 edges

## Surprising Connections (you probably didn't know these)
- `DashboardTest` --references--> `User`  [EXTRACTED]
  tests/Feature/DashboardTest.php → app/Models/User.php
- `ExportReportsTest` --references--> `User`  [EXTRACTED]
  tests/Feature/ExportReportsTest.php → app/Models/User.php
- `SalesAndCriteriaTest` --references--> `User`  [EXTRACTED]
  tests/Feature/SalesAndCriteriaTest.php → app/Models/User.php
- `TransactionAndStockTest` --references--> `User`  [EXTRACTED]
  tests/Feature/TransactionAndStockTest.php → app/Models/User.php
- `TransactionAndStockTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/TransactionAndStockTest.php → app/Models/Location.php

## Import Cycles
- None detected.

## Communities (55 total, 45 thin omitted)

### Community 0 - "Authentication & Routing"
Cohesion: 0.07
Nodes (7): AuthController, Controller, DashboardController, LocationController, StockController, UserController, AdminMiddleware

### Community 1 - "Composer & PHP Dependencies"
Cohesion: 0.06
Nodes (19): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+11 more)

### Community 2 - "Dashboard & Stock Models"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 3 - "Database Migrations"
Cohesion: 0.06
Nodes (8): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, TransferStockRequest, UpdateLocationRequest, UpdateUserRequest

### Community 4 - "Form Requests & Stock Controllers"
Cohesion: 0.10
Nodes (7): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction

### Community 5 - "User & Stock Movement Entities"
Cohesion: 0.09
Nodes (6): UserFactory, DatabaseSeeder, InitialStockSeeder, LocationSeeder, ProductSeeder, UserSeeder

### Community 6 - "Frontend Package Config"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 8 - "Database & Cache Configuration"
Cohesion: 0.15
Nodes (3): ProductController, UpdateProductRequest, Product

### Community 12 - "Console Artisan Commands"
Cohesion: 0.21
Nodes (3): ExampleTest, ResellerLifecycleTest, TestCase

### Community 16 - "App Configuration"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

## Knowledge Gaps
- **21 isolated node(s):** `layouts.app`, `layouts.app`, `layouts.app`, `layouts.app`, `layouts.app` (+16 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 247 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Logging Configuration` to `Authentication & Routing`, `Database Migrations`, `User & Stock Movement Entities`, `Database Seeders & Initial Data`, `Console Artisan Commands`, `Unit & Feature Tests`, `Docker Entrypoint Automation`, `Filesystem Configuration`, `Queue Configuration`, `Services Configuration`, `Auth Views`?**
  _High betweenness centrality (0.085) - this node is a cross-community bridge._
- **What connects `layouts.app`, `layouts.app`, `layouts.app` to the rest of the system?**
  _21 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Authentication & Routing` be split into smaller, more focused modules?**
  _Cohesion score 0.06594071385359952 - nodes in this community are weakly interconnected._
- **Why does `Location` connect `Artisan CLI Binary` to `Authentication & Routing`, `Database Migrations`, `Form Requests & Stock Controllers`, `User & Stock Movement Entities`, `Database Seeders & Initial Data`, `Application Bootstrap & Exceptions`, `Service Providers`, `Console Artisan Commands`, `Unit & Feature Tests`, `Docker Entrypoint Automation`, `Filesystem Configuration`, `Queue Configuration`, `Services Configuration`?**
  _High betweenness centrality (0.072) - this node is a cross-community bridge._
- **Should `Composer & PHP Dependencies` be split into smaller, more focused modules?**
  _Cohesion score 0.06262626262626263 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Database & Cache Configuration` to `Authentication & Routing`, `Form Requests & Stock Controllers`, `User & Stock Movement Entities`, `Database Seeders & Initial Data`, `Application Bootstrap & Exceptions`, `Service Providers`, `Unit & Feature Tests`, `Docker Entrypoint Automation`, `Filesystem Configuration`, `Queue Configuration`, `Services Configuration`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **Should `Dashboard & Stock Models` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._