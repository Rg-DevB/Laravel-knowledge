# API Documentation

## Authentication

All API endpoints require authentication via Laravel Sanctum tokens.

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

## Search

### Advanced Search
Search across problems, solutions, and users.

```http
GET /api/search
Authorization: Bearer {token}
```

**Query Parameters:**

| Parameter | Type   | Required | Description                                      |
|-----------|--------|----------|--------------------------------------------------|
| `q`       | string | Yes      | Search query (min 2, max 100 characters)         |
| `type`    | string | No       | Filter by type: `all`, `problems`, `solutions`, `users` (default: `all`) |
| `limit`   | integer| No       | Number of results (1-50, default: 10)            |

**Example Request:**
```bash
curl -X GET "https://example.com/api/search?q=laravel&type=all&limit=20" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Example Response:**
```json
{
  "results": [
    {
      "type": "problem",
      "id": 1,
      "title": "Laravel Authentication Issue",
      "excerpt": "Having trouble with Laravel Sanctum authentication...",
      "url": "https://example.com/problems/1",
      "meta": "Posted by John Doe • 5 solutions",
      "relevance": 100
    }
  ],
  "total": 1
}
```

## Rate Limiting

API endpoints are rate limited to prevent abuse:
- Search endpoint: 30 requests per minute
- General API: 60 requests per minute

When rate limit is exceeded, you'll receive a `429 Too Many Requests` response.

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "q": ["The q field must be at least 2 characters."]
  }
}
```
