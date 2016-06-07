# Update course

Update a specific course identified by the id.

```
PUT /courses/:id
```

Parameters:

- `id` (required) - The ID of a course
- `name` (required) - The name of a course

## Success

HTTP status: `204 No Content`

## Failure

### Parameter(s) missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"id",
		"name"
	]
}
```

### 2200: The course could not get updated.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "2200",
			"message": "The course could not get updated."
		}
	]
}
```