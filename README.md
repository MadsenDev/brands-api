# Brands API Documentation

## Base URL

All URLs referenced in the API documentation have the following base:

```
https://api.madsens.dev/brands/
```

## Authentication

To authenticate, include your API key in the header of your HTTP request:

```http
API_KEY: your_api_key_here
```

---

## Endpoints

### List All Brands

- **URL**: `/brands.php`
- **Method**: `GET`
- **Response Format**: JSON

#### Example Request

```bash
curl -H "API_KEY: your_api_key_here" https://api.madsens.dev/brands/brands.php
```

#### Example Response

```json
[
    {
        "id": "1",
        "name": "BrandName1",
        "logo": "BrandLogoURL1",
        "website": "BrandWebsite1"
    },
    // ...
]
```

---

### Get Single Brand

- **URL**: `/brands.php?id={id}`
- **Method**: `GET`
- **Response Format**: JSON

#### Example Request

```bash
curl -H "API_KEY: your_api_key_here" https://api.madsens.dev/brands/brands.php?id=1
```

#### Example Response

```json
{
    "id": "1",
    "name": "BrandName1",
    "logo": "BrandLogoURL1",
    "website": "BrandWebsite1"
}
```

---

### Create a New Brand

- **URL**: `/brands.php`
- **Method**: `POST`
- **Response Format**: JSON
- **Payload**: JSON object with `name`, `logo`, and `website`

#### Example Request

```bash
curl -H "API_KEY: your_api_key_here" -H "Content-Type: application/json" -d '{"name": "NewBrand", "logo": "NewLogoURL", "website": "NewWebsite"}' https://api.madsens.dev/brands/brands.php
```

#### Example Response

```json
{
    "id": "2"
}
```

---

### Update a Brand

- **URL**: `/brands.php?id={id}`
- **Method**: `PUT`
- **Response Format**: JSON
- **Payload**: JSON object with `name`, `logo`, and `website`

#### Example Request

```bash
curl -X PUT -H "API_KEY: your_api_key_here" -H "Content-Type: application/json" -d '{"name": "UpdatedBrand", "logo": "UpdatedLogoURL", "website": "UpdatedWebsite"}' https://api.madsens.dev/brands/brands.php?id=1
```

#### Example Response

```json
{
    "message": "Brand updated successfully"
}
```

---

### Delete a Brand

- **URL**: `/brands.php?id={id}`
- **Method**: `DELETE`
- **Response Format**: JSON

#### Example Request

```bash
curl -X DELETE -H "API_KEY: your_api_key_here" https://api.madsens.dev/brands/brands.php?id=1
```

#### Example Response

```json
{
    "message": "Brand deleted successfully"
}
```
