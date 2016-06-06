# Delete course

Delete a specific course identified by the id.

```
DELETE /courses/:id
```

Parameters:

- `id` (required) - The ID of a course

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

### 2401: The course is still linked to a change.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "2401",
			"message": "The course is still linked to a change."
		}
	]
}
```

### 2400: The course could not get deleted.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "2400",
			"message": "The course could not get deleted."
		}
	]
}
```
