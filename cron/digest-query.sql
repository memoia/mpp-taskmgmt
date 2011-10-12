-- IMM4MPP/ES@IT T.451 2009-12-29
-- $Id: digest-query.sql 1032 2009-12-30 22:28:32Z ian $

-- Build result set for the digest emails sent to Eric


USE taskmgmt;


SET SESSION group_concat_max_len = 1024000;
-- SET SESSION max_allowed_packet= 1048576;

SELECT
    t.tasks_id,
    c.name AS category,
    t.name,
    DATE_FORMAT(t.created,'%m/%d/%y') AS created,
    DATE_FORMAT(t.deadline,'%m/%d/%y') AS deadline,
    t.priority,
    CASE t.status
          WHEN 1 THEN 'Open'
          WHEN 2 THEN 'Backburner'
          ELSE t.status
    END AS status_name,
    GROUP_CONCAT(CONCAT('\t\t--- ',
                        DATE_FORMAT(i.updated,'%m/%d %h:%i%p'),
                        ' ---\n',
                        i.body) 
                ORDER BY i.updated DESC 
                SEPARATOR '\n\n') AS digest,
    (SELECT body FROM tasks_info 
      WHERE tasks_info.tasks_id = t.tasks_id 
      ORDER BY tasks_info_id DESC LIMIT 1) AS recent_body
  FROM tasks t
  INNER JOIN categories c ON (c.categories_id = t.categories_id1)
  LEFT JOIN (
    SELECT *
    FROM tasks_info
    WHERE updated >= CURRENT_DATE - INTERVAL 2 WEEK
  ) i ON (t.tasks_id = i.tasks_id)
  WHERE 
    t.status <> 0
    AND DATE(t.deadline) >= CURRENT_DATE
  GROUP BY t.tasks_id
  ORDER BY
    t.priority DESC,
    t.deadline

