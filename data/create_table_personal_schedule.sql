CREATE TABLE IF NOT EXISTS personal_schedule (
						username VARCHAR(20) NOT NULL,
						tenant VARCHAR(50) NOT NULL,
						presentationId INT(3) NOT NULL,
						choice VARCHAR(20) NOT NULL,
						PRIMARY KEY(username, tenant, presentationId)
					);