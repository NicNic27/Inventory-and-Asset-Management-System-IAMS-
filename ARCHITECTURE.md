# AMS Inventory Architecture

## Decision

The AMS Inventory System uses a **Modular Monolithic Architecture**.

It is one Laravel application, one deployable unit, and one primary database. Business capabilities are organized into explicit modules inside the monolith. Modules communicate through application services and, where useful, internal domain events. They are not independent network services.

## Current Shape


Browser
  -> Laravel routes
  -> Authentication and role middleware
  -> Controllers
  -> Domain services
  -> Eloquent models
  -> Shared database
  -> Blade views or JSON responses
```

The current implementation is a modular monolith in transition:

- Routes and controllers are grouped by staff, admin, and frontuser workflows.
- Domain services already exist for assets, supplies, RIS, QR/barcode compatibility, and transactions.
- Some older controllers still contain business logic directly.
- Database transactions protect several inventory and custody mutations.
- Activity logs provide a cross-module audit trail.

## Module Boundaries

### Identity and Access

Owns users, authentication, roles, account status, and profile data.

Primary code:

- `app/Models/User.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Middleware/RoleMiddleware.php`

### Asset Management

Owns fixed assets, property identifiers, asset status, asset images, asset details, and asset lifecycle operations.

Primary code:

- `app/Models/Asset.php`
- `app/Services/AssetService.php`
- `app/Http/Controllers/AssetController.php`
- `app/Http/Controllers/Admin/AssetController.php`

### Asset Custody

Owns issuing, returning, and transferring accountable assets.

Primary code:

- `app/Models/AssetCustody.php`
- `app/Http/Controllers/AssetCustodyController.php`

Custody changes must remain transactional with the asset update and movement ledger entry.

### Supply Inventory

Owns consumable supply records, quantities, stock thresholds, stock movements, and supply images.

Primary code:

- `app/Models/Supply.php`
- `app/Services/SupplyService.php`
- `app/Http/Controllers/SupplyController.php`
- `app/Http/Controllers/Admin/SupplyController.php`

Stock changes must use a row lock and a database transaction so concurrent requests cannot overwrite quantities.

### QR Identity and Scanning

Owns QR rendering, QR scanning, lookup payload normalization, and compatibility with the existing property identifier stored in `barcode_id`.

Primary code:

- `app/Services/BarcodeService.php`
- `app/Http/Controllers/BarcodeController.php`
- `resources/views/barcodes/`
- `resources/views/admin/barcodes/`

The user-facing system uses QR codes. The legacy `barcode_id` column and route names remain compatibility details until a separately planned data migration is approved.

### RIS Requests

Owns requisition creation, line items, status transitions, approval, decline, and supply release behavior.

Primary code:

- `app/Models/RisRequest.php`
- `app/Models/RisItem.php`
- `app/Services/RisService.php`
- `app/Http/Controllers/User/RisController.php`
- `app/Http/Controllers/RisController.php`
- `app/Http/Controllers/Admin/RisController.php`

RIS approval and stock release should be consolidated behind an application service before adding asynchronous side effects.

### ICS and Custodial Documents

Owns ICS creation, signed document upload, item transfer, and digital/sticker views.

Primary code:

- `app/Models/IcsRequest.php`
- `app/Http/Controllers/IcsController.php`

### Procurement

Owns purchase orders, purchase-order items, delivery state, and handoff into inventory registration.

Primary code:

- `app/Models/PurchaseOrder.php`
- `app/Models/PurchaseOrderItem.php`
- `app/Http/Controllers/PurchaseOrderController.php`
- `app/Http/Controllers/Admin/PoController.php`

### Transaction Ledger and Audit

Owns movement history, activity logs, transaction reporting, and cross-module audit visibility.

Primary code:

- `app/Models/Transaction.php`
- `app/Models/ActivityLog.php`
- `app/Services/TransactionService.php`

The ledger is shared infrastructure. It should receive well-defined entries from owning modules rather than becoming a place for unrelated business rules.

## Module Rules

1. Controllers handle transport concerns: authorization context, request validation, response formatting, and redirects.
2. Application services handle use cases and business invariants.
3. Models handle persistence mapping and relationships.
4. A module must not reach into another module's tables to implement its core rules when an application service can express the dependency.
5. Inventory and custody mutations must use database transactions.
6. Cross-module side effects should be introduced through internal domain events after the primary transaction succeeds.
7. Events should represent business facts such as `AssetReturned`, `StockReleased`, or `RisApproved`, not low-level model saves.
8. Queued listeners are appropriate for notifications, reporting, document generation, and integrations. They must not replace the transaction that protects the inventory balance.
9. New code should use QR terminology at the presentation and service API boundaries. Existing `barcode_id` storage may remain until migration planning is complete.
10. Modules share the monolith's database for now. No module should assume that splitting into a network service is imminent.

## Target Request Flow

```text
HTTP request
  -> role middleware
  -> module controller
  -> module application service
  -> database transaction
  -> module models and shared ledger
  -> internal domain event
  -> optional queued listeners
  -> response
```

## Event Adoption Plan

Events are an incremental capability, not a requirement for every operation.

Recommended first events:

- `AssetIssued`
- `AssetReturned`
- `AssetTransferred`
- `StockReceived`
- `StockReleased`
- `RisSubmitted`
- `RisApproved`
- `RisDeclined`

The event should be dispatched only after the database transaction commits. A later outbox table can be introduced if events need to leave the application reliably.

## Not Microservices

This project is deliberately not being split into microservices at this stage. There is currently one codebase, one deployment, and a shared relational data model. A module may be extracted later only after its ownership, API contract, data dependencies, and operational requirements are stable.

## Refactoring Priorities

1. Move remaining controller business logic into module application services.
2. Consolidate RIS approval and supply release into one transactional use case.
3. Keep all supply mutations behind the row-locking supply service.
4. Standardize transaction types and ledger creation.
5. Add focused domain events after core service boundaries are stable.
6. Add module-level tests around assets, custody, supplies, RIS, QR lookup, and procurement handoff.
7. Consider normalizing ICS item JSON and transaction references as separate data-model improvements.
