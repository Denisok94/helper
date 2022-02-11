Инструкции по обновлению для Helper Class
=========================================


Upgrade from Helper 0.7.4
-----------------------
- add:
    - ArrayHelper: 
        - implodeMulti()
        - arrayToObject()
        - array2Object()
        - objectToArray()
        - object2Array()
- edit:
    - StringHelper::replaceBBCode() add: `hr, h1-6, ul, ol,li`

If you use Yii2 and class YiiHelper 

- rename YiiHelper → Helper

`denisok94\helper\YiiHelper` → `denisok94\helper\yii2\Helper`
