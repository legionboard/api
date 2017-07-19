# Update teacher

Update a specific teacher identified by an ID.

```
PUT /teachers/:id
```

Parameters:

- `id` (required) - The ID of a teacher
- `name` (required) - The name of a teacher
- `subject` - ID of a subject
- `archived` (required) - Boolean whether a teacher is archived (true|false)

## Success

HTTP status: `204 No Content`

## Failure

### Parameter(s) missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"id",
		"archived",
		"name"
	]
}
```

### 201: The parameter archived may only contain true or false.

HTTP status: `400 Conflict`

Error code: `201`
> The parameter archived may only contain true or false


### 200: The teacher could not get updated.

HTTP status: `409 Conflict`

Error code: `200`
> The teacher could not get updated.
