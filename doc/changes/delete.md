# Delete change

Delete a specific change identified by the id.

```
DELETE /changes/:id
```

Parameters:

- `id` (required) - The ID of a change

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

### 1400: The change could not get deleted.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "1400",
			"message": "The change could not get deleted."
		}
	]
}
```
