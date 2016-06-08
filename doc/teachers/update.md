# Update teacher

Update a specific teacher identified by an ID.

```
PUT /teachers/:id
```

Parameters:

- `id` (required) - The ID of a teacher
- `name` (required) - The name of a teacher

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

### 200: The teacher could not get updated.

HTTP status: `409 Conflict`

Error code: `200`
> The teacher could not get updated.
