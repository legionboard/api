# Create teacher

Create a new teacher.

```
POST /teachers
```

Parameters:

- `name` (required) - The name of a teacher

## Success

HTTP status: `201 Created`

```json
{
	"id": 6
}
```

## Failure

### Name missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"name"
	]
}
```

### 301: A teacher with the given name already exists.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "301",
			"message": "A teacher with the given name already exists."
		}
	]
}
```

### 300: The teacher could not get created.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "300",
			"message": "The teacher could not get created."
		}
	]
}
```
