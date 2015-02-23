ALTER TABLE #__sdi_map_tool ADD COLUMN activated integer DEFAULT 0;

UPDATE #__sdi_map_tool SET activated=1;
