CREATE TABLE tree(
        id VARCHAR(36) PRIMARY KEY,
        name VARCHAR(20) NOT NULL,
        parent_id VARCHAR(36) DEFAULT NULL,
        lft INT NOT NULL,
        rgt INT NOT NULL,
        position INT NOT NULL
);

insert into tree (id, parent_id, lft, rgt, position, name)
          VALUES ( '1', '', 1, 22, 1, 'Clothing'),
                    ( '2', '1', 2, 9, 1, 'Men''s'),
                      ( '4', '2', 3, 8, 1, 'Suits'),
                        ( '5', '4', 4, 5, 1, 'Slacks'),
                        ( '6', '4', 6, 7, 2, 'Jackets'),
                    ( '3', '1', 10, 21, 2, 'Women''s'),
                      ( '7', '3', 11, 16, 1, 'Dresses'),
                        ('10', '7', 12, 13, 1, 'Evening Gowns'),
                        ('11', '7', 14, 15, 2, 'Sun Dresses'),
                      ( '8', '3', 17, 18, 2, 'Skirts'),
                      ( '9', '3', 19, 20, 3, 'Blouses'),
                  ( '99', '', 23, 24, 1, 'Other')
