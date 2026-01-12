# API Integration Guide

This document describes the API endpoints needed for the BDUI transportation form to work properly.

## Endpoints

### 1. Load Step Data

**GET** `/api/dbui/transportation/{stepId}`

Returns the BDUI JSON structure for a specific step.

**Response:**
```json
{
  "source": { ... },
  "rows": [ ... ],
  "currentStepId": "transportationStep",
  "sidebar": { ... },
  "draftId": "uuid-here"
}
```

**Implementation:**
```php
Route::get('/api/dbui/transportation/{stepId}', function ($stepId) {
    $filePath = resource_path("BDUI/TR/{$stepId}.json");
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'Step not found'], 404);
    }
    $data = json_decode(file_get_contents($filePath), true);
    return response()->json($data);
});
```

### 2. Save Draft

**PUT** `/api/transportations/draft/{draftId}`

Saves form data as a draft.

**Request Body:**
```json
{
  "step": "transportationStep",
  "data": {
    "name": "Transportation Name",
    "pickupFrom": "2024-01-01T10:00:00",
    "pickupTo": "2024-01-01T18:00:00"
  }
}
```

**Response:**
```json
{
  "success": true,
  "draftId": "uuid-here"
}
```

**Implementation:**
```php
Route::put('/api/transportations/draft/{draftId}', function ($draftId, Request $request) {
    // Save to database or cache
    Cache::put("transportation_draft_{$draftId}", [
        'step' => $request->input('step'),
        'data' => $request->input('data'),
        'updated_at' => now(),
    ], now()->addDays(7));
    
    return response()->json(['success' => true, 'draftId' => $draftId]);
});
```

### 3. Load Draft

**GET** `/api/transportations/draft/{draftId}`

Loads saved draft data.

**Response:**
```json
{
  "step": "transportationStep",
  "data": {
    "name": "Transportation Name",
    "pickupFrom": "2024-01-01T10:00:00",
    "pickupTo": "2024-01-01T18:00:00"
  }
}
```

**Implementation:**
```php
Route::get('/api/transportations/draft/{draftId}', function ($draftId) {
    $draft = Cache::get("transportation_draft_{$draftId}");
    if (!$draft) {
        return response()->json(['error' => 'Draft not found'], 404);
    }
    return response()->json($draft);
});
```

### 4. Create Transportation

**POST** `/api/transportations`

Creates the final transportation record.

**Request Body:**
```json
{
  "name": "Transportation Name",
  "pickupFrom": "2024-01-01T10:00:00",
  "pickupTo": "2024-01-01T18:00:00",
  "pickupAddress": { ... },
  "deliveryAddress": { ... },
  "cargos": [ ... ],
  "status": "confirmed"
}
```

**Response:**
```json
{
  "success": true,
  "transportation": {
    "id": "uuid-here",
    "name": "Transportation Name",
    ...
  }
}
```

**Implementation:**
```php
Route::post('/api/transportations', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'pickupFrom' => 'required|date',
        'pickupTo' => 'required|date|after:pickupFrom',
        // ... other validation rules
    ]);
    
    // Create transportation
    $transportation = Transportation::create([
        'id' => Str::uuid(),
        'name' => $validated['name'],
        'pickup_from' => $validated['pickupFrom'],
        'pickup_to' => $validated['pickupTo'],
        'transportation_status' => $validated['status'] ?? 'new',
        'client_id' => auth()->id(),
    ]);
    
    // Create addresses
    // Create cargos
    // etc.
    
    return response()->json([
        'success' => true,
        'transportation' => $transportation,
    ]);
});
```

### 5. Get Options for Select Fields

**GET** `/api/{source}`

Returns options for select fields (countries, cities, currencies).

**Examples:**
- `/api/countries` - Returns list of countries
- `/api/cities` - Returns list of cities (may need country_id parameter)
- `/api/currencies` - Returns list of currencies

**Response:**
```json
[
  { "id": 1, "name": "United States", "code": "US" },
  { "id": 2, "name": "Canada", "code": "CA" }
]
```

**Implementation:**
```php
Route::get('/api/countries', function () {
    return Country::select('id', 'name', 'code')->get();
});

Route::get('/api/cities', function (Request $request) {
    $query = City::query();
    if ($request->has('country_id')) {
        $query->where('country_id', $request->country_id);
    }
    return $query->select('id', 'name')->get();
});

Route::get('/api/currencies', function () {
    return Currency::select('code', 'name')->get();
});
```

## CSRF Protection

Make sure to include CSRF token in requests:

```javascript
// In your composable or API client
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

fetch('/api/transportations', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    },
    body: JSON.stringify(data),
});
```

## Error Handling

All endpoints should return consistent error responses:

```json
{
  "error": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "pickupFrom": ["The pickup from field must be a valid date."]
  }
}
```

## Authentication

All endpoints should be protected with authentication middleware:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/api/dbui/transportation/{stepId}', ...);
    Route::put('/api/transportations/draft/{draftId}', ...);
    Route::post('/api/transportations', ...);
});
```
