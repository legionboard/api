# Delete teacher

Delete a specific teacher identified by the id.

```
DELETE /teachers/:id
```

Parameters:

- `id` (required) - The ID of a teacher

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

### 401: Deleting the teacher with ID 1 is not allowed.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "401",
			"message": "Deleting the teacher with ID 1 is not allowed."
		}
	]
}
```

### 402: The teacher is still linked to a change.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "402",
			"message": "The teacher is still linked to a change."
		}
	]
}
```

### 400: The teacher could not get deleted.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "400",
			"message": "The teacher could not get deleted."
		}
	]
}
```
