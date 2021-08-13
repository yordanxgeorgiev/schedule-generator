CREATE TABLE IF NOT EXISTS presentations (
		presentationId INT(3) UNSIGNED AUTO_INCREMENT NOT NULL,
		tenant VARCHAR(50) NOT NULL,
		date DATE NOT NULL,
		start VARCHAR(50),
		end VARCHAR(50),
		topic VARCHAR(50),
		names VARCHAR(50),
		ids VARCHAR(50),
		major VARCHAR(50),
		description VARCHAR(100),
		PRIMARY KEY(presentationId, tenant)
		);