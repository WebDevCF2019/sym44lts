SELECT
    a.*,
    GROUP_CONCAT(c2.titre SEPARATOR '|||') AS categtitre,
    GROUP_CONCAT(c2.slug SEPARATOR '|||') AS categslug,
    u.thename,u.thelogin
FROM
    categ c
INNER JOIN categ_has_article cha ON
    cha.categ_idcateg = c.idcateg
INNER JOIN article a ON
    a.idarticle = cha.article_idarticle
INNER JOIN categ_has_article cha2 ON
    a.idarticle = cha2.article_idarticle
INNER JOIN categ c2 ON
    c2.idcateg = cha2.categ_idcateg
INNER JOIN user u  ON
	a.user_iduser = u.iduser
WHERE
    c.idcateg = 74
GROUP BY a.idarticle
ORDER BY a.thedate DESC;