IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='users' AND column_name='points_balance')
THEN
    ALTER TABLE users ADD COLUMN points_balance INT DEFAULT 0;
END IF;