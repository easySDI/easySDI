DECLARE @SchemaName NVARCHAR(256),
        @TableName NVARCHAR(256),
        @SQL NVARCHAR(MAX),
        @NewLine CHAR(1)
 
SELECT  @SchemaName = N'dbo',
        @TableName = N'sdi_sdi_order',
        @NewLine = CHAR(10)
 
SELECT  @SQL = ISNULL(@SQL + @NewLine, '') + 
            'ALTER TABLE [' + S.name + '].[' + T.name + '] ' +
                'DROP CONSTRAINT [' + D.name + ']'
FROM    sys.tables T
    INNER JOIN sys.default_constraints D
        ON D.parent_object_id = T.object_id
    INNER JOIN sys.columns C
        ON C.object_id = T.object_id
            AND C.column_id = D.parent_column_id
    INNER JOIN sys.schemas S
        ON T.schema_id = S.schema_id
WHERE   S.name = @SchemaName
  AND   T.name = @TableName
  AND D.name LIKE '%valid%'
 
PRINT @SQL
EXECUTE (@SQL)