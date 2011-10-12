#!/usr/bin/env python
#
# IMM4MPP/ES@IT T.451 2009-12-29
# $Id: digest-email.py 1032 2009-12-30 22:28:32Z ian $
# 
# Send Eric digest emails of pending tasks with history.
#
# Intended to be run via cron under a user who has a .my.cnf with
# proper connection settings for accessing TASKMGMT on Alaska/Anchorage.
#

import sys, os, MySQLdb, subprocess


taskmgmt_url = "https://..."
interval_sql = "INTERVAL 2 WEEK"
sent_from    = "Alaska T.451 <cronjobs@mppnv.org>"
send_to	     = "taskmgmt-digest@mppnv.org"
subject	     = "TASKMGMT Task Digest"


output = subprocess.Popen("mail -a 'From: %s' -s '%s' %s" % (sent_from,subject,send_to), \
			    shell=True, stdin=subprocess.PIPE).stdin

# To debug without emailing:
# output = sys.stdout



db = MySQLdb.connect(db='taskmgmt',read_default_file="~/.my.cnf")



db.query("SET SESSION group_concat_max_len = 1024000")
c = db.cursor(MySQLdb.cursors.DictCursor)
c.execute(
  """
  SELECT
    t.tasks_id,
    c.name AS category,
    t.name,
    DATE_FORMAT(t.created,'%%m/%%d/%%y') AS created,
    DATE_FORMAT(t.deadline,'%%m/%%d/%%y') AS deadline,
    t.priority,
    CASE t.status
	  WHEN 1 THEN 'Open'
	  WHEN 2 THEN 'Backburner'
	  ELSE t.status
    END AS status_name,
    GROUP_CONCAT(CONCAT('\\t\\t--- ',
			DATE_FORMAT(i.updated,'%%m/%%d %%h:%%i%%p'),
			' ---\\n',
			i.body) 
		ORDER BY i.updated DESC 
		SEPARATOR '\\n\\n') AS digest,
    (SELECT body FROM tasks_info 
      WHERE tasks_info.tasks_id = t.tasks_id 
      ORDER BY tasks_info_id DESC LIMIT 1) AS recent_body
  FROM tasks t
  INNER JOIN categories c ON (c.categories_id = t.categories_id1)
  LEFT JOIN (
    SELECT *
    FROM tasks_info
    WHERE updated >= CURRENT_DATE - %s
  ) i ON (t.tasks_id = i.tasks_id)
  WHERE 
    t.status <> 0
    AND DATE(t.deadline) >= CURRENT_DATE
  GROUP BY t.tasks_id
  ORDER BY
    t.priority DESC,
    t.deadline
  """ % (interval_sql)
)


r = c.fetchone()
print >> output, """
MPP-NV IT TASKMGMT Digest:
  Summary of pending tickets that have been updated
  within %s of today or have an upcoming
  milestone. Complete notes found at
    %s

NOTE: 
  This message may contain MPP Confidential and other
  sensitive material (including passwords) and should 
  not be forwarded.

""" % (interval_sql,taskmgmt_url)
while r is not None:
  print >> output, """



***********************************************************************
T.%s: %s (%s) 
			  created:    %s    status:   %s
			  milestone:  %s    priority: %s
***********************************************************************

%s
  """ % (
      r['tasks_id']
    , r['name']
    , r['category']
    , r['created']
    , r['status_name']
    , r['deadline']
    , r['priority']
    , r['digest'] or "\t\t--- Not updated recently ---\n%s\n\nFor more history, see:\n  %s/history?id=%s"%(r['recent_body'],taskmgmt_url,r['tasks_id'])
  )
  r = c.fetchone()

db.close()


output.close()

