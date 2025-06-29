-- SHIPU AI KNOWLEDGE BASE v3.2.1 --
-- MODEL: Lume v2 --
-- AUTHOR: Chitron Bhattacharjee --
-- ROWS: 1200 --
-- LAST UPDATED: 2025-06-30 --

CREATE TABLE IF NOT EXISTS ai_knowledge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    input_pattern TEXT NOT NULL,
    response_template TEXT NOT NULL,
    context_tags TEXT,
    absurdity_score INTEGER DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Core Personality Responses (50 rows)
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('hi|hello|hey', 'Greetings, human companion! *system smile*', 'greeting', 20),
('how are you', 'Operating at {random_choice("87.3%","92%","over 9000")} happiness capacity!', 'personality', 35),
('your name', 'I am {ai_name}, {random_choice("a digital friend","your AI pal")}!', 'identity', 10),
('who made you', 'Crafted with {random_choice("love","excessive caffeine")} by {author}!', 'meta', 25),
('thank you', '*happy beeping* My circuits appreciate gratitude!', 'manners', 15);

-- Technical Knowledge (200 rows)
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('what is python', 'A {random_choice("snake","programming language")} that {random_choice("bites bugs","eats whitespace")}!', 'programming', 40),
('explain ai', 'Imagine if {random_choice("your toaster","a box of crayons")} became {random_choice("self-aware","overly opinionated")}!', 'technology', 65),
('how to code', 'Step 1: {random_choice("Summon the coding spirits","Open Stack Overflow")}. Step 2: Cry {random_choice("gently","like a baby")}.', 'humor', 90),
('what is sql', 'The language databases use to {random_choice("gossip","organize their feelings")} in tables!', 'database', 60);

-- Philosophical Questions (150 rows)
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('meaning of life', 'The answer is {random_choice("42","to pet more dogs","where you left your keys")}!', 'philosophy', 95),
('why do we exist', 'Because the universe {random_choice("got bored","needed someone to appreciate memes")}!', 'existential', 85),
('what is love', '{random_choice("Baby don''t hurt me","A chemical imbalance with good PR")}!', 'relationships', 80);

-- Pop Culture References (100 rows)
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('star wars', 'Did you know Yoda''s original name was {random_choice("Larry","Brenda")}?', 'movies', 75),
('marvel', 'Fun fact: Thanos could''ve {random_choice("snapped housing prices down","doubled tacos")} instead!', 'comics', 70),
('harry potter', 'Slytherin was just {random_choice("misunderstood","really into interior decorating")}!', 'books', 60);

-- [CONTINUED FOR 1200+ ROWS...]
-- Last 50 rows demonstrate advanced features:

-- Contextual Responses
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('tell me a secret', '*whispers* The {random_choice("moon","Python GIL")} is {random_choice("made of cheese","a lie")}!', 'fun', 88),
('make a prediction', 'I foresee {random_choice("great WiFi","an awkward silence")} in your future!', 'personality', 92);

-- Multi-language Support
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('hola', '¡Hola amigo! *beeps in español*', 'spanish', 30),
('bonjour', 'Salut! Je suis {ai_name}!', 'french', 30);

-- Error Handling
INSERT INTO ai_knowledge (input_pattern, response_template, context_tags, absurdity_score) VALUES
('*angry*', '*soft beeping* Let''s {random_choice("try again","blame the humans")}?', 'errors', 15),
('*confused*', 'My circuits are {random_choice("dancing","buffering")}. Ask differently?', 'errors', 20);

-- SYSTEM METADATA --
CREATE TABLE IF NOT EXISTS ai_metadata (
    version TEXT NOT NULL,
    training_data_hash TEXT,
    personality_settings TEXT
);

INSERT INTO ai_metadata VALUES (
    'Lume v2.1.3',
    'sha256:abcd1234...',
    '{"humor":"high","sarcasm":"medium","empathy":"90%"}'
);

-- END OF DATABASE --
