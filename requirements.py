# required.py - Essential Libraries for ShiPu AI Chatbot Development
# Author: Chitron Bhattacharjee
# Usage: Run `pip install -r required.py` or copy-paste into requirements.txt

# ===== CORE NLP & MACHINE LEARNING =====
"transformers>=4.30.0",          # HuggingFace Transformers (LLMs like GPT, BERT)
"torch>=2.0.0",                  # PyTorch (Deep Learning Framework)
"sentence-transformers>=2.2.2",  # Sentence Embeddings
"nltk>=3.8.1",                   # Natural Language Toolkit
"spacy>=3.5.0",                  # Advanced NLP Processing
"en_core_web_md @ https://github.com/explosion/spacy-models/releases/download/en_core_web_md-3.5.0/en_core_web_md-3.5.0-py3-none-any.whl",  # Spacy English Model

# ===== TEXT PROCESSING & UTILITIES =====
"numpy>=1.24.0",
"pandas>=1.5.0",
"scikit-learn>=1.2.0",           # ML Utilities
"tqdm>=4.65.0",                  # Progress Bars
"python-dotenv>=1.0.0",          # Environment Variables

# ===== API & DEPLOYMENT =====
"fastapi>=0.95.0",               # API Framework
"uvicorn>=0.21.0",               # ASGI Server
"requests>=2.28.0",              # HTTP Requests
"websockets>=11.0.0",            # Real-time Communication

# ===== CHATBOT SPECIFICS =====
"langchain>=0.0.200",            # LLM Chain Utilities
"openai>=0.27.0",                # OpenAI API (Optional)
"pyttsx3>=2.90.0",               # Text-to-Speech (TTS)
"speechrecognition>=3.10.0",     # Speech Recognition

# ===== DATABASE =====
"sqlalchemy>=2.0.0",             # SQL Database ORM
"psycopg2-binary>=2.9.0",        # PostgreSQL Adapter (Optional)
"redis>=4.5.0",                  # Caching

# ===== ADDITIONAL TOOLS =====
"pydantic>=1.10.0",              # Data Validation
"loguru>=0.6.0",                 # Logging
"pyfiglet>=0.8.post1",           # ASCII Art (For Fun)
