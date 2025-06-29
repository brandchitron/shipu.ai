#include <iostream>
#include <sqlite3.h>
#include <vector>
#include <iomanip>

// Structure to hold database entries
struct KnowledgeEntry {
    int id;
    std::string query;
    std::string response;
    int absurdity_level;
};

// Callback function for SQL queries
static int callback(void* data, int argc, char** argv, char** azColName) {
    std::vector<KnowledgeEntry>* entries = static_cast<std::vector<KnowledgeEntry>*>(data);
    
    KnowledgeEntry entry;
    entry.id = std::stoi(argv[0]);
    entry.query = argv[1] ? argv[1] : "";
    entry.response = argv[2] ? argv[2] : "";
    entry.absurdity_level = std::stoi(argv[3]);
    
    entries->push_back(entry);
    return 0;
}

int main() {
    sqlite3* db;
    char* zErrMsg = 0;
    int rc;
    std::vector<KnowledgeEntry> entries;
    
    // Open database (change path to your SQL file)
    rc = sqlite3_open("LLMdatabase.sql", &db);
    
    if (rc) {
        std::cerr << "Can't open database: " << sqlite3_errmsg(db) << std::endl;
        return 1;
    }
    
    std::cout << "Successfully opened ShiPu AI database\n";
    
    // SQL query to read all entries
    const char* sql = "SELECT id, query, response, absurdity_level FROM ai_knowledge;";
    
    // Execute SQL query
    rc = sqlite3_exec(db, sql, callback, &entries, &zErrMsg);
    
    if (rc != SQLITE_OK) {
        std::cerr << "SQL error: " << zErrMsg << std::endl;
        sqlite3_free(zErrMsg);
    } else {
        std::cout << "Loaded " << entries.size() << " knowledge entries\n\n";
        
        // Display first 10 entries as sample
        std::cout << "Sample entries:\n";
        std::cout << std::left << std::setw(6) << "ID" 
                  << std::setw(30) << "Query" 
                  << std::setw(50) << "Response" 
                  << "Absurdity\n";
        std::cout << std::string(100, '-') << "\n";
        
        for (size_t i = 0; i < std::min(entries.size(), size_t(10)); i++) {
            const auto& e = entries[i];
            std::cout << std::left << std::setw(6) << e.id 
                      << std::setw(30) << (e.query.length() > 25 ? e.query.substr(0, 25) + "..." : e.query)
                      << std::setw(50) << (e.response.length() > 45 ? e.response.substr(0, 45) + "..." : e.response)
                      << e.absurdity_level << "\n";
        }
    }
    
    // Close database
    sqlite3_close(db);
    
    return 0;
}
