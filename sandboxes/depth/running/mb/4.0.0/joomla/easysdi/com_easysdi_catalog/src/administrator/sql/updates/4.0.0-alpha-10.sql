UPDATE `#__sdi_sys_stereotype` SET isocode = 'CharacterString', namespace_id = '2' WHERE `value` = 'file';
UPDATE `#__sdi_sys_stereotype` SET isocode = 'URL', namespace_id = '1' WHERE `value` = 'link';

UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '([A-Z0-9]{8}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{12})' WHERE `value` = 'guid';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'text';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'locale';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '[0-9.-]' WHERE `value` = 'number';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '([0-9]{4}-[0-9]{2}-[0-9]{2})' WHERE `value` = 'date';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'list';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|\'|:|\<|$|\.\s)#ie' WHERE `value` = 'link';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '([0-9]{4}-[0-9]{2}-[0-9]{2})' WHERE `value` = 'datetime';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'textchoice';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'localechoice';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'gemet';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '[0-9.-]' WHERE `value` = 'distance';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '[0-9.-]' WHERE `value` = 'integer';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'file';
UPDATE `#__sdi_sys_stereotype` SET defaultpattern = '' WHERE `value` = 'geographicextent';

