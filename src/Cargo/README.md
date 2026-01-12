### Cargo
Main internal entity of transportation. 

Includes a complete description of all spatial, weight and value characteristics of the shipment.

List of required fields:
* `name` - shipment name
* `length` - length in mm
* `width` - width in mm
* `height` - height in mm
* `weight` - weight in grams
* `price` - shipment cost in kopecks/cents
* `currency` - order currency
* `transportation_id` - foreign key for linking with `transportations` table

Cargo cannot exist outside of transportation, which is an aggregate.
