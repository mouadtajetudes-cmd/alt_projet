CREATE TABLE IF NOT EXISTS followers (
    id_follow SERIAL PRIMARY KEY,
    follower_id INTEGER NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    following_id INTEGER NOT NULL REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    date_follow TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_follow_pair UNIQUE (follower_id, following_id),
    CONSTRAINT check_no_self_follow CHECK (follower_id <> following_id)
);

CREATE INDEX IF NOT EXISTS idx_following ON followers(following_id);
CREATE INDEX IF NOT EXISTS idx_follower ON followers(follower_id);