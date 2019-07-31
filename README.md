


SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_quarter_bonus.team_per
FROM
    tp_users

LEFT JOIN tp_quarter_bonus ON (tp_users.user_id = tp_quarter_bonus.user_id)
where tp_users.first_leader = 11469273



select * from tp_users where user_id = 14209459

SELECT COUNT(*) FROM  tp_wx_message

delete from tp_alipay where id < 3000

update tp_users set ach_pool = 1

SELECT SUM(user_money) FROM tp_users