-- Create sections table
CREATE TABLE IF NOT EXISTS tbl_sections (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL,
    section_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Alter tbl_quiz to add section_id
ALTER TABLE tbl_quiz
ADD COLUMN section_id INT,
ADD FOREIGN KEY (section_id) REFERENCES tbl_sections(section_id);

-- Insert some default sections
INSERT INTO tbl_sections (section_name, section_description) VALUES
('History', 'Test your knowledge of historical events and figures'),
('Science', 'Questions about scientific concepts and discoveries'),
('Games', 'Gaming-related trivia and knowledge'),
('Geography', 'Questions about countries, capitals, and landmarks'),
('Sports', 'Sports-related questions and trivia');
