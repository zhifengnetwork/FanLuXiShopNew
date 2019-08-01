
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


SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_account_log.user_money
FROM
    tp_account_log

LEFT JOIN tp_users ON (tp_users.user_id = tp_account_log.user_id)

order by user_money desc



SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_account_log.user_money,
   tp_quarter_bonus.team_per
FROM
    tp_account_log

LEFT JOIN tp_users ON (tp_users.user_id = tp_account_log.user_id)
LEFT JOIN tp_quarter_bonus ON (tp_users.user_id = tp_quarter_bonus.user_id)

where tp_account_log.user_id in (select user_id from tp_users where first_leader = 10968567)

order by user_money desc




SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_quarter_bonus.team_per,
   tp_quarter_bonus.fenhong
   
FROM
    tp_quarter_bonus

LEFT JOIN tp_users ON (tp_users.user_id = tp_quarter_bonus.user_id)

order by fenhong desc



SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_quarter_bonus.team_per,
   tp_quarter_bonus.fenhong,
   tp_quarter_bonus.level,
   sum(tp_quarter_bonus.fenhong) as fff
   
FROM
    tp_quarter_bonus

LEFT JOIN tp_users ON (tp_users.user_id = tp_quarter_bonus.user_id)
where tp_quarter_bonus.fenhong > 0 and tp_quarter_bonus.level >= 4
order by fenhong desc

# 终极

SELECT
   tp_users.user_id,
   tp_users.nickname,
   tp_quarter_bonus.team_per,
   tp_quarter_bonus.fenhong,
   tp_quarter_bonus.level

FROM
    tp_quarter_bonus

LEFT JOIN tp_users ON (tp_users.user_id = tp_quarter_bonus.user_id)
where tp_quarter_bonus.fenhong > 1 and tp_quarter_bonus.level >= 4
order by fenhong desc

