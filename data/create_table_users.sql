CREATE TABLE IF NOT EXISTS users (
						username VARCHAR(20) NOT NULL,
						password VARCHAR(255) NOT NULL,
						tenant VARCHAR(50) NOT NULL,
						PRIMARY KEY(username, tenant)
					);	