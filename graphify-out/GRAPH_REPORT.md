# Graph Report - scentpeep-management-system  (2026-10-27)

## Corpus Check
- 115 files · ~56,947 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 18, .example 2, .conf 2)

## Summary
- 517 nodes · 853 edges · 54 communities (9 shown, 45 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Database Migrations
- HTTP Controllers
- Eloquent Models
- Form Request Validation
- Blade View Templates
- Authentication & Authorization
- Stock Management Logic
- Transaction Processing
- Dashboard Analytics
- User Management
- Product Catalog
- Location Management
- Report Generation
- Database Seeders
- Unit & Feature Tests
- Frontend Assets
- Configuration Files
- Service Providers
- Middleware
- Console Commands
- Route Definitions
- Module 23
- Module 25
- Module 26
- Module 27
- Module 28
- Module 29
- Module 30
- Module 31
- Module 32
- Module 33
- Module 34
- Module 35
- Module 36
- Module 37
- Module 38
- Module 39
- Module 40
- Module 41
- Module 42
- Module 43

## God Nodes (most connected - your core abstractions)
1. `User` - 41 edges
2. `Location` - 37 edges
3. `Transaction` - 28 edges
4. `Product` - 27 edges
5. `TestCase` - 20 edges
6. `TransactionAndStockTest` - 16 edges
7. `Stock` - 15 edges
8. `UserController` - 12 edges
9. `TransactionController` - 11 edges
10. `DashboardTest` - 11 edges

## Surprising Connections (you probably didn't know these)
- `DashboardTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/DashboardTest.php → app/Models/Location.php
- `ExportReportsTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/ExportReportsTest.php → app/Models/Location.php
- `SalesAndCriteriaTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/SalesAndCriteriaTest.php → app/Models/Location.php
- `TransactionAndStockTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/TransactionAndStockTest.php → app/Models/Location.php
- `DashboardTest` --references--> `Product`  [EXTRACTED]
  tests/Feature/DashboardTest.php → app/Models/Product.php

## Import Cycles
- None detected.

## Communities (54 total, 45 thin omitted)

### Community 0 - "Database Migrations"
Cohesion: 0.06
Nodes (19): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+11 more)

### Community 1 - "HTTP Controllers"
Cohesion: 0.08
Nodes (7): AuthController, Controller, DashboardController, LocationController, ProductController, StockController, UserController

### Community 2 - "Eloquent Models"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 3 - "Form Request Validation"
Cohesion: 0.05
Nodes (9): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, TransferStockRequest, UpdateLocationRequest, UpdateProductRequest (+1 more)

### Community 4 - "Blade View Templates"
Cohesion: 0.10
Nodes (7): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction

### Community 5 - "Authentication & Authorization"
Cohesion: 0.09
Nodes (6): UserFactory, DatabaseSeeder, InitialStockSeeder, LocationSeeder, ProductSeeder, UserSeeder

### Community 8 - "Dashboard Analytics"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 10 - "Product Catalog"
Cohesion: 0.17
Nodes (4): ExampleTest, ResellerLifecycleTest, SecurityHeadersTest, TestCase

### Community 16 - "Configuration Files"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

## Knowledge Gaps
- **21 isolated node(s):** `concurrently`, `tailwindcss`, `@laravel/multiplex`, `layouts.app`, `layouts.app` (+16 more)
  These have ≤1 connection - possible missing edges. (Counts symbols only; 252 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **45 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `User Management` to `HTTP Controllers`, `Form Request Validation`, `Authentication & Authorization`, `Stock Management Logic`, `Transaction Processing`, `Product Catalog`, `Report Generation`, `Database Seeders`, `Unit & Feature Tests`, `Service Providers`, `Middleware`, `Route Definitions`?**
  _High betweenness centrality (0.092) - this node is a cross-community bridge._
- **What connects `concurrently`, `tailwindcss`, `@laravel/multiplex` to the rest of the system?**
  _21 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Database Migrations` be split into smaller, more focused modules?**
  _Cohesion score 0.06262626262626263 - nodes in this community are weakly interconnected._
- **Why does `Location` connect `Database Seeders` to `HTTP Controllers`, `Form Request Validation`, `Blade View Templates`, `Authentication & Authorization`, `Stock Management Logic`, `Transaction Processing`, `User Management`, `Product Catalog`, `Location Management`, `Report Generation`, `Unit & Feature Tests`, `Service Providers`, `Middleware`, `Route Definitions`?**
  _High betweenness centrality (0.076) - this node is a cross-community bridge._
- **Should `HTTP Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.08244897959183674 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Middleware` to `HTTP Controllers`, `Blade View Templates`, `Authentication & Authorization`, `Stock Management Logic`, `Transaction Processing`, `User Management`, `Location Management`, `Report Generation`, `Database Seeders`, `Unit & Feature Tests`, `Service Providers`, `Route Definitions`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Should `Eloquent Models` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._