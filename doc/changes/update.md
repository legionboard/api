# Update change

Update a specific change identified by the id.

```
PUT /changes/:id
```

Parameters:

- `id` (required) - The ID of a change
- `teacher` (required) - ID of a teacher linked to a change
- `coveringTeacher` (required when type == 1) - ID of a teacher covering the lesson
- `startBy` (required) - Time when a change starts (YYYY-MM-DDT[lesson])
- `endBy` (required) - Time when a change ends (YYYY-MM-DDT[lesson])
- `type` (required) - Type of change (0: cancellation, 1: cover, 2: information)
- `text` (required when type == 2) - Text describing a change
- `reason` - The reason for a change (0: ill, 1: official, 2: on leave)

## Success

HTTP status: `204 No Content`

## Failure

### Parameter(s) missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"id",
		"teacher",
		"coveringTeacher",
		"startBy",
		"endBy",
		"type",
		"text"
	]
}
```

### 1201/1202: The starting/ending time is formatted badly.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1201",
			"message": "The starting time is formatted badly."
		},
		{
			"code": "1202",
			"message": "The ending time is formatted badly."
		}
	]
}
```

### 1203/1204: The starting/ending time does not exist.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1203",
			"message": "The starting time does not exist."
		},
		{
			"code": "1204",
			"message": "The ending time does not exist."
		}
	]
}
```

### 1205/1206: The (covering) teacher may only contain an integer.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1205",
			"message": "The teacher may only contain an integer."
		},
		{
			"code": "1206",
			"message": "The covering teacher may only contain an integer."
		}
	]
}
```

### 1207: The type is not allowed.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1207",
			"message": "The type is not allowed."
		}
	]
}
```

### 1208/1209: The (covering) teacher does not exist.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1208",
			"message": "The teacher does not exist."
		},
		{
			"code": "1209",
			"message": "The covering teacher does not exist."
		}
	]
}
```

### 1210: The ending time has to be after the start time.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1210",
			"message": "The ending time has to be after the start time."
		}
	]
}
```

### 1211: The reason is not allowed.

HTTP status: `400 Bad Request`

```json
{
	"error": [
		{
			"code": "1211",
			"message": "The reason is not allowed."
		}
	]
}
```

### 1200: The change could not get updated.

HTTP status: `409 Conflict`

```json
{
	"error": [
		{
			"code": "1200",
			"message": "The change could not get updated."
		}
	]
}
```
