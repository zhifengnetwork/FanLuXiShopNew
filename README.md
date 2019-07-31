


SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_quarter_bonus.team_per
FROM
    tp_users

LEFT JOIN tp_quarter_bonus ON (tp_users.user_id = tp_quarter_bonus.user_id)
where tp_users.first_leader = 11469273