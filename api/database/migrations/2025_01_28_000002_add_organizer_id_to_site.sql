-- Modify organizer_id column to match organizers table
ALTER TABLE site MODIFY COLUMN organizer_id BIGINT UNSIGNED NULL;

-- Add foreign key constraint
ALTER TABLE site ADD CONSTRAINT fk_site_organizer 
FOREIGN KEY (organizer_id) REFERENCES organizers(id) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Add index for better performance
CREATE INDEX idx_site_organizer_id ON site(organizer_id); 