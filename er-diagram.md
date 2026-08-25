```mermaid
erDiagram

    users {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    books {
        bigint id PK
        bigint user_id FK
        varchar title UK
        varchar author
        text description
        varchar isbn UK
        date published_date
        varchar image_url
        timestamp created_at
        timestamp updated_at
    }

    genres {
        bigint id PK
        varchar name UK
        timestamp created_at
        timestamp updated_at
    }

    favorites {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        timestamp created_at
        timestamp updated_at
    }

    reviews {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        tinyint rating
        text comment
        timestamp updated_at
        timestamp created_at
    }

    like_review {
        bigint id PK
        bigint user_id FK
        bigint review_id FK
        timestamp created_at
        timestamp updated_at
    }

    book_genre {
        bigint id PK
        bigint book_id FK
        bigint genre_id FK
        timestamp created_at
        timestamp updated_at
    }

    reading_plans {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        date target_date
        varchar status
        timestamp completed_at
        timestamp created_at
        timestamp updated_at
    }

    notifications {
        bigint id PK
        bigint user_id FK
        text message
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }



    users ||--o{ books : "登録"


    users ||--o{ favorites : "お気に入り"
    books ||--o{ favorites : ""

    users ||--o{ reviews : "レビュー投稿"
    books ||--o{ reviews : ""

    users ||--o{ reading_plans : "読書計画"
    books ||--o{ reading_plans : ""

    reviews ||--o{ like_review : "いいね"
    users ||--o{ like_review : ""

    genres ||--o{ book_genre : "ジャンル紐付け"
    books ||--o{ book_genre : ""

    users ||--o{ notifications : "通知受信"
```