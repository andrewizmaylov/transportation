## Transportation section

Main aggregate of the transportation system. Belongs to the `Shipper` aggregate

A registered and confirmed* `Shipper` user in the system can create a `Transportation` request.

Request lifecycle:
* Registration of the request in the system
* Setting pickup and delivery addresses
* Formation of `Cargo` list. One transportation can contain an unlimited number of `Cargo` items provided they use the same cargo pickup and delivery addresses.
* Publication of the request in public access. At the `Transportation` level, this means changing the status from `New` to `Confirmed`



### Guests can
* See all confirmed transportations with partially hidden information
* See individual Transportation card without ability to communicate

### Users can
* See full Transportation information
* Join communication and conversation
* Pickup Transportation order
* Submit and edit Transportation

### Admin can
