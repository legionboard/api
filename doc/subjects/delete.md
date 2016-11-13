# Delete subject

Delete a specific subject identified by an ID.

```
DELETE /subjects/:id
```

Parameters:

- `id` (required) - The ID of a subject

## Success

HTTP status: `204 No Content`

## Failure

### ID missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"id"
	]
}
```

### 3401: The subject is still linked to a change.

HTTP status: `400 Bad Request`

Error code: `3401`
> The subject is still linked to a change.

### 3400: The subject could not get deleted.

HTTP status: `409 Conflict`

Error code: `3400`
> The subject could not get deleted.
