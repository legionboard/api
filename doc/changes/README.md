# Changes

## GET

* [List changes](list.md)

## POST

* [Create change](create.md)

## PUT

* [Update change](update.md)

## DELETE

* [Delete change](delete.md)

## Notes

There are two special hours:

* `startBy` with the argument 0 is considered as the beginning of the
day. If you are developing a client, make sure that this is set as
default value. Also do not show this value when showing the changes.
* `endBy` with the argument 20 is considered as the ending of the
day. If you are developing a client, make sure that this is set as
default value. Also do not show this value when showing the changes.
