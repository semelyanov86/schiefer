

/* For old migrated versions which dont have Calls icon in tools panel */
DELIMITER $$

DROP PROCEDURE IF EXISTS return_calls $$

CREATE PROCEDURE `return_calls`()
BEGIN
    SET @old_tabid = (SELECT DISTINCT tabid FROM vtiger_tab WHERE name = 'PBXManager');
    IF (@old_tabid IS NOT NULL) THEN
        IF NOT EXISTS (SELECT 1 FROM vtiger_app2tab WHERE tabid = @old_tabid) THEN
            SET @sequence = (SELECT MAX(sequence) FROM vtiger_app2tab WHERE appname = 'TOOLS') + 1;
            INSERT INTO vtiger_app2tab (tabid, appname, sequence, visible) VALUES (@old_tabid, 'TOOLS', @sequence, 1);
        END IF;
    END IF;
END $$

CALL return_calls() $$
DROP PROCEDURE IF EXISTS return_calls $$

DELIMITER ;



/* Cloud voip additional settings */
CREATE TABLE vtiger_sp_voipintegration_options(
    `name` varchar(255) NOT NULL,
    `value` varchar(255) DEFAULT NULL,
    UNIQUE(`name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO vtiger_sp_voipintegration_options(`name`,`value`) VALUES('use_click_to_call', '0');



/* Update PDF templates for not rounding products quantity in invetory part */
UPDATE sp_templates SET template = '{header}

<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>

<p style="font-weight: bold">Адрес: {$orgBillingAddress}, тел.: {$orgPhone}</p>

<div style="font-weight: bold; text-align: center">Образец заполнения платежного поручения</div>

<table border="1" cellpadding="2">
<tr>
  <td width="140">ИНН {$orgInn}</td><td width="140">КПП {$orgKpp}</td><td rowspan="2" width="50"><br/><br/>Сч. №</td><td rowspan="2" width="200"><br/><br/>{$orgBankAccount}</td>
</tr>
<tr>
<td colspan="2" width="280"><span style="font-size: 8pt">Получатель</span><br/>{$orgName}</td>
</tr>
<tr>
<td colspan="2" rowspan="2" width="280"><span style="font-size: 8pt">Банк получателя</span><br/>{$orgBankName}</td>
<td width="50">БИК</td>
<td rowspan="2" width="200">{$orgBankId}<br/>{$orgCorrAccount}</td>
</tr>
<tr>
<td width="50">Сч. №</td>
</tr>
</table>
<br/>
<h1 style="text-align: center">СЧЕТ № {$invoice_no} от {$invoice_invoicedate}</h1>
<br/><br/>
<table border="0">
<tr>
<td width="100">Плательщик:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
<tr>
<td width="100">Грузополучатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
</table>

{/header}

{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
    <tr style="text-align: center; font-weight: bold">
	<td width="30">№</td>
      <td width="260">Наименование<br/>товара</td>
      <td width="65">Единица<br/>изме-<br/>рения</td>
      <td width="35">Коли-<br/>чество</td>
	<td width="70">Цена</td>
	<td width="70">Сумма</td>
	</tr>
{/table_head}

{table_row}
    <tr>
	<td width="30">{$productNumber}</td>
      <td width="260">{$productName} {$productComment}</td>
	<td width="65" style="text-align: center">{$productUnits}</td>
	<td width="35" style="text-align: right">{$productQuantity}</td>
	<td width="70" style="text-align: right">{$productPriceWithTax}</td>
	<td width="70" style="text-align: right">{$productTotal}</td>
    </tr>
{/table_row}

{summary}
</table>
<table border="0" style="font-size: 8pt;font-weight: bold">
    <tr>
      <td width="450">
        <table border="0" cellpadding="2">
          <tr><td width="450" style="text-align: right">Итого:</td></tr>
          <tr><td width="450" style="text-align: right">В т.ч. НДС:</td></tr>
          <tr><td width="450" style="text-align: right">Всего к оплате:</td></tr>
        </table>
      </td>
      <td width="70">
        <table border="1" cellpadding="2">
          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>
          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>
          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>
        </table>
      </td>
  </tr>
</table>

<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>

{/summary}

{ending}
<br/>
    <p>Руководитель предприятия  __________________ ( {$orgDirector} ) <br/>
    <br/>
    Главный бухгалтер  __________________ ( {$orgBookkeeper} )
    </p>
{/ending}' where name = 'Счет';

UPDATE sp_templates SET template = '
{header}
<h1 style="font-size: 14pt">Расходная накладная № {$salesorder_no}</h1>
<hr/>
<p><br></p>
<table border="0" style="font-size: 9pt">
<tr>
<td width="80">Поставщик:</td><td width="450"><span style="font-weight: bold">{$orgName}</span></td>
</tr>
<tr>
<td width="80">Покупатель:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>
</tr>
</table>
{/header}

{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
    <tr style="text-align: center; font-weight: bold">
	<td width="30" rowspan="2">№</td>
	<td width="200" rowspan="2">Товар</td>
	<td width="50" rowspan="2" colspan="2">Мест</td>
	<td width="60" rowspan="2" colspan="2">Количество</td>
	<td width="60" rowspan="2">Цена</td>
	<td width="60" rowspan="2">Сумма</td>
	<td width="70">Номер ГТД</td>
    </tr>
    <tr style="text-align: center; font-weight: bold">
	<td width="70">Страна<br/>происхождения</td>
    </tr>
{/table_head}

{table_row}
    <tr>
	<td width="30" rowspan="2">{$productNumber}</td>
	<td width="200" rowspan="2">{$productName}</td>
	<td width="25" rowspan="2"></td>
	<td width="25" rowspan="2">шт.</td>
	<td width="30" rowspan="2" style="text-align: right">{$productQuantity}</td>
	<td width="30" rowspan="2">{$productUnits}</td>
	<td width="60" rowspan="2" style="text-align: right">{$productPrice}</td>
	<td width="60" rowspan="2" style="text-align: right">{$productNetTotal}</td>
	<td width="70">{$customsId}</td>
    </tr>
    <tr>
	<td width="70">{$manufCountry}</td>
    </tr>
{/table_row}

{summary}
</table>
<p></p>
<table border="0" style="font-weight: bold">
    <tr>
	<td width="400" style="text-align: right">Итого:</td>
	<td width="60" style="text-align: right">{$summaryNetTotal}</td>
    </tr>
    <tr>
	<td width="400" style="text-align: right">Сумма НДС:</td>
	<td width="60" style="text-align: right">{$summaryTax}</td>
    </tr>
</table>

<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>

{/summary}

{ending}
    <hr size="2">
    <p><br></p>
    <table border="0">
    <tr>
	<td>Отпустил  __________ </td><td>Получил  __________ </td>
    </tr>
    </table>
{/ending}' where name = 'Накладная';

UPDATE sp_templates SET template = '
{header}
<h1 style="font-size: 14pt">Заказ на закупку № {$purchaseorder_no}</h1>
<hr>
<p><br></p>
<table border="0" style="font-size: 9pt">
<tr>
<td width="80">Поставщик:</td><td width="450"><span style="font-weight: bold">{$vendor_vendorname}</span></td>
</tr>
<tr>
<td width="80">Покупатель:</td><td width="450"><span style="font-weight: bold">{$orgName}</span></td>
</tr>
</table>
{/header}
{table_head}
<table border="1" style="font-size: 8pt" cellpadding="2">
<tr style="text-align: center; font-weight: bold">
<td width="30">№</td>
<td width="200">Товар</td>
<td width="60" colspan="2">Количество</td>
<td width="60">Цена</td>
<td width="60">Сумма</td>
</tr>
{/table_head}
{table_row}
<tr>
<td width="30">{$productNumber}</td>
<td width="200">{$productName}</td>
<td width="30" style="text-align: right">{$productQuantity}</td>
<td width="30">{$productUnits}</td>
<td width="60" style="text-align: right">{$productPrice}</td>
<td width="60" style="text-align: right">{$productNetTotal}</td>
</tr>
{/table_row}
{summary}
</table>
<p></p>
<table border="0" style="font-weight: bold">
<tr>
<td width="350" style="text-align: right">Итого:</td>
<td width="60" style="text-align: right">{$summaryNetTotal}</td>
</tr>
<tr>
<td width="350" style="text-align: right">Сумма НДС:</td>
<td width="60" style="text-align: right">{$summaryTax}</td>
</tr>
</table>
<p>
Всего наименований {$summaryTotalItems}, на сумму {$summaryGrandTotal} руб.<br/>
<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>
</p>
{/summary}
{ending}
{/ending}' where name = 'Заказ на закупку';

UPDATE sp_templates SET template = '
{header}
<!-- Table marking up header, goods table and footer -->
<table>
<tr><td colspan="2">

  <table>
    <tr>
      <td colspan="3" rowspan="2" style="width:700px">
      </td>
      <td style="text-align:right; font-size:x-small; width:80px">
        Унифицированная форма № ТОРГ-12
        Утверждена постановлением Госкомстата России от 25.12.98 № 132
      </td>
    </tr>
    <tr>
      <td style="width:80px">
        <table border="1">
          <tr><td style="text-align:center">Коды</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Грузоотправитель</td>
      <td style="font-size:x-small; font-weight:bold; width:520px">{$orgName}, {$orgBillingAddress}, тел. : {$orgPhone}, р/с {$orgBankAccount}, {$orgBankName}, БИК {$orgBankId}, к/с {$orgCorrAccount}</td>
      <td rowspan="8" style="width:100px">
        <table style="text-align:right; font-size:small">
          <tr><td>Форма по ОКУД</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td></td></tr>
          <tr><td>Вид деятельности по ОКДП</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td>по ОКПО</td></tr>
          <tr><td rowspan="4">
            <table border="1">
              <tr><td>номер</td></tr>
              <tr><td>дата</td></tr>
              <tr><td>номер</td></tr>
              <tr><td>дата</td></tr>
            </table>
            <table>
              <tr><td>Вид операции</td></tr>
            </table>
            </td></tr>
        </table>
      </td>
      <td rowspan="8" style="width:80px">
        <table border="2" style="text-align:center; font-size:small">
          <tr><td style="font-weight:bold">0330212</td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
          <tr><td></td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Структурное подразделение</td>
      <td style="font-size:x-small; width:400px"></td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Грузополучатель</td>
      <td style="font-size:x-small; width:400px">{$account_accountname}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Поставщик</td>
      <td style="font-size:x-small; width:400px">{$orgName}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Плательщик</td>
      <td style="font-size:x-small; width:400px">{$account_accountname}</td>
    </tr>
    <tr>
      <td style="font-size:x-small; width:80px">Основание</td>
      <td style="font-size:x-small; width:400px"></td>
    </tr>
    <tr>
      <td style="width:80px"></td>
      <td style="text-align: center; font-size:xx-small; width:400px">наименование документа (договор, контракт, заказ-наряд)</td>
    </tr>
  </table>

</td></tr>
<tr>
  <td colspan="2" style="text-align:center; width:600px">
    <!-- Markup inside heading elements -->
    <table cellspacing="5" style="font-size:x-small">
      <tr>
        <td></td>
        <td rowspan="2">
          <table border="1" style="text-align:center">
            <tr>
              <td>Номер документа</td>
              <td>Дата составления</td>
            </tr>
            <tr>
              <td>{$consignment_goods_consignment_no}</td>
              <td>{$consignment_consignmentdate_short}</td>
            </tr>
          </table>
        </td>
        <td>Транспортная накладная</td>
      </tr>
      <tr>
        <td style="font-weight:bold; text-align:right">ТОВАРНАЯ НАКЛАДНАЯ</td>
        <td></td>
      </tr>
    </table>
  </td>
</tr>
<tr><td>
<!-- Whitespace between header and goods table -->
</td></tr>
{/header}
{table_head}
<tr><td colspan="2">

  <!-- Goods table -->
  <table border="1" style="font-size:x-small; text-align:center; width:780px">
    <tr style="text-align:center">
      <td style="width:30px">Номер<br />по<br />порядку</td>
      <td colspan="2" style="width:210px">Товар</td>
      <td colspan="2" style="width:80px">Единица измерения</td>
      <td style="width:40px">Вид упаковки</td>
      <td colspan="2" style="width:80px">Количество</td>
      <td style="width:50px">Масса брутто</td>
      <td style="width:50px">Количество<br />(масса нетто)</td>
      <td style="width:50px">Цена, руб. коп.</td>
      <td style="width:50px">Сумма без учёта НДС, руб. коп.</td>
      <td colspan="2" style="width:60px">НДС</td>
      <td style="width:80px">Сумма с учётом НДС, руб. коп.</td>
    </tr>
    <tr>
      <td style="width:30px"></td>
      <td style="width:180px">наименование, характеристика, сорт, артикул товара</td>
      <td style="width:30px">код</td>
      <td style="width:50px">наименование</td>
      <td style="width:30px">код по ОКЕИ</td>
      <td style="width:40px"></td>
      <td style="width:40px">в одном<br />месте</td>
      <td style="width:40px">мест,<br />штук</td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:30px">ставка, %</td>
      <td style="width:30px">сумма, руб. коп.</td>
      <td style="width:80px"></td>
    </tr>
    <tr>
      <td style="width:30px">1</td>
      <td style="width:180px">2</td>
      <td style="width:30px">3</td>
      <td style="width:50px">4</td>
      <td style="width:30px">5</td>
      <td style="width:40px">6</td>
      <td style="width:40px">7</td>
      <td style="width:40px">8</td>
      <td style="width:50px">9</td>
      <td style="width:50px">10</td>
      <td style="width:50px">11</td>
      <td style="width:50px">12</td>
      <td style="width:30px">13</td>
      <td style="width:30px">14</td>
      <td style="width:80px">15</td>
    </tr>
{/table_head}
{goods_row}
    <tr>
      <td style="width:30px">{$goodsNumber}</td>
      <td style="width:180px">{$productName}</td>
      <td style="width:30px">{$productCode}</td>
      <td style="width:50px">{$productUnits}</td>
      <td style="width:30px">{$productUnitsCode}</td>
      <td style="width:40px"></td>
      <td style="width:40px"></td>
      <td style="width:40px">{$productQuantity}</td>
      <td style="width:50px"></td>
      <td style="width:50px"></td>
      <td style="width:50px">{$productPrice}</td>
      <td style="width:50px">{$productNetTotal}</td>
      <td style="width:30px">{$productTaxPercent}</td>
      <td style="width:30px">{$productTax}</td>
      <td style="width:80px">{$productTotal}</td>
    </tr>
{/goods_row}
{summary}
  </table>

</td></tr>
<!-- Total subtable -->
<tr style="font-size:x-small">
  <!-- Whitespace in the left -->
  <td style="width:403px">

    <table>
      <tr>
        <td colspan="7" style="text-align:right">Итого:</td>
      </tr>
      <tr>
        <td colspan="7" style="text-align:right">Всего по накладной:</td>
      </tr>
    </table>

  </td>
  <!-- Total subtable itself -->
  <td>

    <table border="1" style="text-align:center">
      <tr>
        <td style="width:40px"></td>
        <td style="width:50px"></td>
        <td style="width:50px"></td>
        <td style="width:50px">X</td>
        <td style="width:50px">{$summaryNetTotalGoods}</td>
        <td style="width:30px">X</td>
        <td style="width:30px">{$summaryTaxGoods}</td>
        <td style="width:80px">{$summaryGrandTotalGoods}</td>
      </tr>
      <tr>
        <td style="width:40px"></td>
        <td style="width:50px"></td>
        <td style="width:50px"></td>
        <td style="width:50px">X</td>
        <td style="width:50px">{$summaryNetTotalGoods}</td>
        <td style="width:30px">X</td>
        <td style="width:30px">{$summaryTaxGoods}</td>
        <td style="width:80px">{$summaryGrandTotalGoods}</td>
      </tr>
    </table>

  </td>
</tr>
<tr style="page-break-before: always"><td>
<!-- Whitespace -->
</td></tr>
<tr><td colspan="2" style="width:780px">

  <table style="font-size:x-small">
    <tr>
      <td rowspan="2" style="width:250px">Товарная накладная имеет приложение на </td>
      <td style="text-align: center; width:350px">______________________________________________________________________</td>
      <td rowspan="2" style="width:80px"> листах</td>
    </tr>
    <tr>
      <td style="text-align:center; width:350px">прописью</td>
    </tr>
    <tr>
      <td rowspan="2" style="width:250px">и содержит </td>
      <td style="text-align: center; width:350px">______________________________________________________________________</td>
      <td rowspan="2" style="width:80px"> порядковых номеров записей</td>
    </tr>
    <tr>
      <td style="text-align:center; width:350px">прописью</td>
    </tr>
  </table>

</td></tr>
<tr><td colspan="2" style="text-align:center; width:780px">

  <table style="font-size:x-small">
    <tr>
      <td colspan="2" rowspan="2" style="width:200px"></td>
      <td rowspan="2" style="text-align:right; width:190px">Масса груза (нетто)</td>
      <td style="text-align:center; width:390px">______________________________________________________________________</td>
    </tr>
    <tr>
      <td style="text-align:center; width:390px">прописью</td>
    </tr>
    <tr>
      <td rowspan="2" style="text-align:right; width:100px">Всего мест</td>
      <td rowspan="2" style="width:100px">{$summaryTotalGoods}</td>
      <td rowspan="2" style="text-align:right; width:190px">Масса груза (брутто)</td>
      <td style="text-align:center; width:390px">______________________________________________________________________</td>
    </tr>
    <tr>
      <td style="text-align:center; width:390px">прописью</td>
    </tr>
    <tr>
      <td colspan="3" style="width:390px">
        <table>
          <tr>
            <td rowspan="2" style="width:170px">Приложение (паспорта, сертификаты и т.п.) на </td>
            <td style="text-align:center; width:170px">__________________________________________</td>
            <td rowspan="2" style="width:50px"> листах</td>
          </tr>
          <tr>
            <td style="text-align:center; width:170px">прописью</td>
          </tr>
        </table>
      </td>
      <td></td>
    </tr>
  </table>

</td></tr>
<tr><td>
</td></tr>
<!-- Footer (sent/received, stamps, etc.) -->
<tr style="font-size:small">
  <td style="text-align:center; width:390px">
    <!-- "Sent" part on the left -->
    <table>
      <tr><td>
        Всего отпущено на сумму {$summaryGrandTotalGoodsLiteral}<br />
      </td></tr>
{/summary}
{ending}
      <tr><td>
        <table>
          <tr>
            <td rowspan="2">Отпуск груза разрешил</td>
            <td>Директор</td>
            <td>_____________________</td>
            <td>{$orgDirector}</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <tr>
            <td colspan="2" rowspan="2" style="font-weight:bold">Главный (старший) бухгалтер</td>
            <td>_____________________</td>
            <td>{$orgBookkeeper}</td>
          </tr>
          <tr style="font-size:x-small">
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <tr>
            <td rowspan="2">Отпуск груза произвёл</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        М.П. "   " _____________ 20   года
      </td></tr>
    </table>
  </td>
  <!-- "Received" part on the right -->
  <td style="text-align:center; width:390px">
    <table>
      <tr><td>
        По доверенности № _______ от   "   " _____________ 20   года
      </td></tr>
      <tr><td>
        выданной ________________________________________________
      </td></tr>
      <tr><td style="text-align:center; font-size:x-small">
        кем, кому (организация, место работы, должность, фамилия, и. о.)
      </td></tr>
      <tr><td>
        <table>
          <tr>
            <td rowspan="2">Груз принял</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
          <!-- Whitespace to aline with left-side clauses -->
          <tr>
            <td></td>
          </tr>
          <tr>
            <td rowspan="2">Груз получил<br />грузополучатель</td>
            <td>_____________________</td>
            <td>_____________________</td>
            <td>_____________________</td>
          </tr>
          <tr style="font-size:x-small">
            <td>должность</td>
            <td>подпись</td>
            <td>расшифровка подписи</td>
          </tr>
        </table>
      </td></tr>
      <tr><td>
        М.П. "   " _____________ 20   года
      </td></tr>
    </table>
  </td>
</tr>

</table>
{/ending} ' where name='ТОРГ-12';





/* SPTips migration */


DROP TABLE sp_tips_cur_provider;

/* Providers data migrate */
SET @DADATA_API_KEY = (SELECT CASE WHEN api_key IS NULL THEN '' ELSE api_key END FROM sp_tips_providers WHERE provider_name='DaData');
SET @GOOGLE_API_KEY = (SELECT CASE WHEN api_key IS NULL THEN '' ELSE api_key END FROM sp_tips_providers WHERE provider_name='Google');

ALTER TABLE sp_tips_providers ADD COLUMN settings varchar(1024) DEFAULT '{}';
ALTER TABLE sp_tips_providers DROP COLUMN api_key;
ALTER TABLE sp_tips_providers DROP COLUMN token;

UPDATE sp_tips_providers SET settings=CONCAT('{"api_key":"', @DADATA_API_KEY, '"}') WHERE provider_name='DaData';
UPDATE sp_tips_providers SET settings=CONCAT('{"api_key":"', @GOOGLE_API_KEY, '"}') WHERE provider_name='Google';


/* Update rules table */
ALTER TABLE sp_tips_module_rules DROP COLUMN provider_field;
ALTER TABLE sp_tips_module_rules ADD COLUMN type VARCHAR(255);
UPDATE sp_tips_module_rules SET type='address';

/* Update ids generation */
SET @LAST_RULE_ID = (SELECT IFNULL(MAX(rule_id),0) FROM sp_tips_module_rules);
CREATE TABLE sp_tips_module_rules_seq(
    `id` int(19)
);
INSERT INTO sp_tips_module_rules_seq VALUES(@LAST_RULE_ID + 1);


SET @LAST_DEPENDENT_ID = (SELECT IFNULL(MAX(field_id),0) FROM sp_tips_dependent_fields);
CREATE TABLE sp_tips_dependent_fields_seq(
    `id` int(19)
);
INSERT INTO sp_tips_dependent_fields_seq VALUES(@LAST_DEPENDENT_ID + 1);








/* Google contacts sync fix */
CREATE TABLE IF NOT EXISTS `vtiger_google_sync_localization` (
`id` int(19) NOT NULL,
`field_name` varchar(150) NOT NULL,
`original_field_type` varchar(150) DEFAULT NULL,
`russian_field_type` varchar(150) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `vtiger_google_sync_localization` VALUES
(1, 'gd:namePrefix', '', ''),
(2, 'gd:givenName', '', ''),
(3, 'gd:familyName', '', ''),
(4, 'gd:orgTitle', '', ''),
(5, 'gd:orgName', '', ''),
(6, 'gContact:birthday', '', ''),
(7, 'gd:email', 'home', 'Домашние контакты'),
(8, 'gd:email', 'work', 'Рабочие контакты'),
(9, 'gd:phoneNumber', 'mobile', 'Мобильные устройства'),
(10, 'gd:phoneNumber', 'work', 'Рабочие контакты'),
(11, 'gd:phoneNumber', 'home', 'Домашние контакты'),
(12, 'gd:structuredPostalAddress', 'home', 'Домашние контакты'),
(13, 'gd:structuredPostalAddress', 'work', 'Рабочие контакты'),
(14, 'content', '', '');
