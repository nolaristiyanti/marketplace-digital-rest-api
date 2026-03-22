GET /api/products
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/fdcefdb4-df57-4f5d-9228-48b9e99b7ac3" />

GET /api/products/{id}
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/9f469b38-5473-45b5-8b29-e5bc65640716" />

POST /api/products
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/42b5f687-ee0e-448f-96c7-3ea35b3ffc74" />

PUT /api/products
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/c2f41028-ee7f-4d2a-9921-3003f7cd4e94" />

DELETE /api/products
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/d597bf53-e66f-4bed-8b70-0997f86345b8" />

GET /api/categories
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/e20b3670-8e8a-4e08-a907-1f62169e84c9" />

GET /api/categories/{id}
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/e1c37002-594c-43af-b2e7-bb622e5b4b2f" />

POST /api/categories
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/6e4e9de8-901e-4d6f-9e13-e98ce148d998" />

PUT /api/categories/{id}
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/9462d5ef-e762-47ca-8b5b-5f81d316ce37" />

DELETE /api/categories/{id}
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/66fd0698-34de-4f47-b35d-5c5bed774922" />

Filter & Search
GET /api/products?search=ui kit - Cari berdasarkan judul produk
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/e03685ca-7a7f-4da9-91aa-f37dcc135adf" />

GET /api/products?category_id=1 - Filter berdasarkan kategori
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/0c6c12e0-e493-41b7-9958-7e117f4186ae" />

GET /api/products?min_price=10000&max_price=50000 - Filter range harga
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/84f16c70-62ab-4009-abca-63119ef17c75" />

GET /api/products?sort_by=rating&order=desc - Urutkan by rating
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/ab0a6f97-b291-452a-9e83-175ac2b62dcf" />

GET /api/products?sort_by=price&order=asc - Urutkan by harga
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/3cae9898-eb21-403a-9fb1-c4e072c9efdd" />

GET /api/products?sort_by=download_count&order=desc - Lihat produk paling laris
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/6ad002c3-7c4b-4b9c-8c9a-a82fcdcd09ba" />

Rating Classification
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/a3af924e-ddb8-4f6d-aed5-c73dc30ad275" />

Struktur Response API
Success Response
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/342562f2-ef06-4cfa-b04e-e826dd200bd3" />

Error Response (Validation)
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/fc9529d3-9497-410c-a091-2228248dbbce" />

Not Found Response (404)
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/ea069410-9dbe-4c37-aa84-c2e86e012957" />

Unauthorized Response (403)
<img width="940" height="556" alt="image" src="https://github.com/user-attachments/assets/7518d378-fb02-43b8-857f-1547bb1beb24" />
