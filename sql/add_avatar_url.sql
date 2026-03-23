-- Add avatar_url column to utilisateurs table
ALTER TABLE utilisateurs 
ADD COLUMN IF NOT EXISTS avatar_url TEXT;

-- Add online status columns
ALTER TABLE utilisateurs 
ADD COLUMN IF NOT EXISTS last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS is_online BOOLEAN DEFAULT FALSE;

-- Create index for faster online status queries
CREATE INDEX IF NOT EXISTS idx_utilisateurs_is_online ON utilisateurs(is_online);
CREATE INDEX IF NOT EXISTS idx_utilisateurs_last_seen ON utilisateurs(last_seen);
