<?php 
  require_once('../Connections/dspp.php');
  require_once('../mpdf/mpdf.php');
  /** Se agrega la libreria PHPExcel */
  require_once '../PHPExcel/PHPExcel.php';
  mysql_select_db($database_dspp, $dspp);
  function mayuscula($variable) {
    $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    return $variable;
  }


  ////********* ****** GENERAMOS LA LISTA DE OPPS EN PDF
  if(isset($_POST['lista_publica_pdf']) && $_POST['lista_publica_pdf'] == 1){
    $query = $_POST['query_pdf'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error());


    $html = '

      <div>
        <table border="1">
          <tr style="background-color:#B8D186">
            <td style="text-align: center;">#</td>
            <td style="text-align: center;width:300px;">
              NOMBRE DE LA ORGANIZACIÓN / ORGANIZATION´S NAME
            </td>
            <td style="text-align: center;">
              ABREVIACIÓN / SHORT NAME
            </td>
            <td style="text-align: center;">
              PAÍS / COUNTRY
            </td>
            <td style="text-align: center;width:300px;">
              PRODUCTO(S) CERTIFICADO/ CERTIFIED PRODUCTS
            </td>
            <td style="text-align: center;">
              FECHA SIGUIENTE EVALUACIÓN/ NEXT EVALUATION DATE
            </td>
            <!--<td style="text-align: center;">
              ESTATUS / STATUS
            </td>-->
            <td style="text-align: center;">
              ENTIDAD QUE OTORGÓ EL CERTIFICADO / ENTITY THAT GRANTED CERTIFICATE
            </td>
            <td style="text-align: center;">
              IDENTIFICACIÓN / IDENTIFICATION
            </td>
            <td style="text-align: center;">
              SITIO WEB / WEB SITE
            </td>
            <td style="text-align: center;">
              CORREO ELECTRÓNICO / EMAIL
            </td>
            <td style="text-align: center;">
              TELÉFONO / TELEPHONE
            </td>
          </tr>
    ';
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $row_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $producto = '';
      $total_producto = mysql_num_rows($row_producto);
      $cont = 1;
      while($detalle_producto = mysql_fetch_assoc($row_producto)){
        if($cont < $total_producto){
          $producto .= $detalle_producto['producto'].', ';
        }else{
          $producto .= $detalle_producto['producto'];
        }
        $cont++;
      }

      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = '<p style="color:#e74c3c">No Disponible</p>';
      }

      $html .= '
      <tr>
        <td style="font-size:12px;text-align: left;">'.$contador.'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['nombre'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['abreviacion'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['pais'].'</td>
        <td style="font-size:12px;text-align: left;">'.$producto.'</td>
        <td style="font-size:12px;text-align: center;">'.$vigencia.'</td>
        <!--<td style="font-size:12px;text-align: left;">'.$opp['nombre_publico'].'</td>-->
        <td style="font-size:12px;text-align: center;">'.$opp['abreviacion_oc'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['spp'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['sitio_web'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['email'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['telefono'].'</td>
      </tr>';
      $contador++;
    }
    $html .='
      </table>
      <h2>NOTAS: <span style="color:#e74c3c">ES RESPONSABILIDAD DEL INTERESADO REVISAR EL ESTATUS ESPECÍFICO EN EL QUE SE ENCUENTRA LA OPP</span></h2>
      
    </div>';

    $mpdf = new mPDF('c', 'A2');
    $mpdf->setAutoTopMargin = 'pad'; //activamos el margin-top para que respete el header
    $mpdf->keep_table_proportions = TRUE; //deshabilitamos el auto-size de la tabla
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                  <div>
                <img src="img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Lista de Organizaciones de Pequeños Productores /List of Small Producers´ Organizations
                </h2>             
                  </div>
                  <div>Símbolo de Pequeños Productores</div>
                  <div>'.date('d/m/Y', time()).'</div>
            </td>
          </tr>
        </table>
      </div>
    </header>
      ');
    $css = file_get_contents('css/style.css');  
    $mpdf->AddPage('L');


    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($html);
    $mpdf->Output('reporte.pdf', 'I');
  }

  if(isset($_POST['lista_pdf']) && $_POST['lista_pdf'] == 1){
    $query = $_POST['query_pdf'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error());


    $html = '

      <div>
        <table border="1" >
          <tr style="background-color:#B8D186">
            <td style="text-align: center;">#</td>
            <td style="text-align: center;">
              NOMBRE DE LA ORGANIZACIÓN / ORGANIZATION´S NAME
            </td>
            <td style="text-align: center;">
              ABREVIACIÓN / SHORT NAME
            </td>
            <td style="text-align: center;">
              PAÍS / COUNTRY
            </td>
            <td style="text-align: center;">
              PRODUCTO(S) CERTIFICADO/ CERTIFIED PRODUCTS
            </td>
            <td style="text-align: center;">
              FECHA SIGUIENTE EVALUACIÓN/ NEXT EVALUATION DATE
            </td>
            <td style="text-align: center;">
              ESTATUS / STATUS
            </td>
            <td style="text-align: center;">
              ENTIDAD QUE OTORGÓ EL CERTIFICADO / ENTITY THAT GRANTED CERTIFICATE
            </td>
            <td style="text-align: center;">
              IDENTIFICACIÓN / IDENTIFICATION
            </td>
            <td style="text-align: center;">
              SITIO WEB / WEB SITE
            </td>
            <td style="text-align: center;">
              CORREO ELECTRÓNICO / EMAIL
            </td>
            <td style="text-align: center;">
              TELÉFONO / TELEPHONE
            </td>
            <td style="text-align: center;">
              CONTACTOS / CONTACTS
            </td>

          </tr>
    ';
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
      $row_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $producto = '';
      $total_producto = mysql_num_rows($row_producto);
      $row_contactos = mysql_query("SELECT * FROM contactos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total_contactos = mysql_num_rows($row_contactos);

      $cont = 1;
      while($detalle_producto = mysql_fetch_assoc($row_producto)){
        if($cont < $total_producto){
          $producto .= $detalle_producto['producto'].', ';
        }else{
          $producto .= $detalle_producto['producto'];
        }
        $cont++;
      }

      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = '<p style="color:#e74c3c">No Disponible</p>';
      }

      $html .= '
      <tr>
        <td style="font-size:12px;text-align: left;">'.$contador.'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['nombre'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['abreviacion_opp'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['pais'].'</td>
        <td style="font-size:12px;text-align: left;">'.$producto.'</td>
        <td style="font-size:12px;text-align: center;">'.$vigencia.'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['nombre_publico'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['abreviacion_oc'].'</td>
        <td style="font-size:12px;text-align: center;">'.$opp['spp_opp'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['sitio_web'].'</td>
        <td style="font-size:12px;text-align: left;width: 10%">'.$opp['email'].'</td>
        <td style="font-size:12px;text-align: left;">'.$opp['telefono'].'</td>
        <td style="font-size:12px;text-align: left;">';
        if($total_contactos){
          $html .= '
            <table border="1" style="margin:0px;padding:0px;">
              <tr style="width:400px;">
                <td style="text-align: center;">Nombre</td>
                <td style="text-align: center;">Cargo</td>
                <td style="text-align: center;">Email</td>
                <td style="text-align: center;">Telefono</td>
              </tr>';
              while($contactos = mysql_fetch_assoc($row_contactos)){
                $html .= '
                <tr style="width:400px;">
                  <td style="text-align: left;">'.$contactos['nombre'].'</td>
                  <td style="text-align: left;">'.$contactos['cargo'].'</td>
                  <td style="text-align: left;">
                    Email 1: <span style="color:red">'.$contactos['email1'].'</span><br>
                    Email 2: <span style="color:red">'.$contactos['email2'].'</span>
                  </td>                
                  <td style="text-align: left;">
                    Tel 1: <span style="color:red">'.$contactos['telefono1'].'</span><br>
                    Tel 2: <span style="color:red">'.$contactos['telefono2'].'</span>
                  </td>
                </tr>';
              }
        $html .= '
            </table>';
        }else{
          $html .= '<strong style="color:red">No Disponible</strong>';
        }
      $html .= '
        </td>
      </tr>';
      $contador++;
    }
    $html .='
      </table>
      <h2>NOTAS:</h2>
      <h2>1. El estatus de \'En Revisión\' significa que la OPP puede encontrarse en cualquiera de los siguientes sub estatus: \'En proceso de renovación\', \'Certificado expirado\' o \'Suspendido\' </h2>
      <h2>2. Es responsabilidad de los interesados verificar si la OPP se encuentran en proceso de renovación del certificado, cuando en la presente lista se indica que el estatus es "En Revisión"</h2>
      <h2>3. El estatus de \'Cancelado\' siginifica que la OPP ya no esta certificada por Incumplimiento con el Marco Regulatorio SPP o por renuncia voluntaria. Si fue cancelado por incumpliento con el marco regulatorio, deberá esperar dos años a partir de la cancelación para volver a solicitar la certificación.</h2>

    </div>';

    $mpdf = new mPDF('c', 'A2');
    $mpdf->setAutoTopMargin = 'pad';
    $mpdf->keep_table_proportions = TRUE;
    $mpdf->SetHTMLHeader('
    <header class="clearfix">
      <div>
        <table style="padding:0px;margin-top:-20px;">
          <tr>
            <td style="text-align:left;margin-bottom:0px;font-size:12px;">
                  <div>
                <img src="img/FUNDEPPO.jpg" >
                  </div>
            </td>
            <td style="text-align:right;font-size:12px;">
                  <div>
                <h2>
                  Lista de Organizaciones de Pequeños Productores /List of Small Producers´ Organizations
                </h2>             
                  </div>
                  <div>Símbolo de Pequeños Productores</div>
                  <div>'.date('d/m/Y', time()).'</div>
            </td>
          </tr>
        </table>
      </div>
    </header>
      ');
    $css = file_get_contents('css/style.css');  
    $mpdf->AddPage('L');
    $mpdf->pagenumPrefix = 'Página / Page ';
    $mpdf->pagenumSuffix = ' - ';
    $mpdf->nbpgPrefix = ' de ';
    //$mpdf->nbpgSuffix = ' pages';
    $mpdf->SetFooter('{PAGENO}{nbpg}');
    $mpdf->writeHTML($css,1);
    $mpdf->writeHTML($html);
    $mpdf->Output('reporte.pdf', 'I');

  }

  ////********* ****** GENERAMOS LA LISTA DE OPPS EN EXCEL
  if(isset($_POST['lista_publica_excel']) && $_POST['lista_publica_excel'] == 2){
    $query = $_POST['query_excel'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error()); 
    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setSubject("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setDescription("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setKeywords("LISTA ORGANIZACIONES DE PEQUEÑOS PRODUCTORES")
               ->setCategory("LISTA ORGANIZACIONES");

    $tituloReporte = "Lista de Organizaciones de Pequeños Productores /List of Small Producers´ Organizations ";
    $titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO(S) CERTIFICADO', 'FECHA SIGUIENTE EVALUACIÓN', 'ENTIDAD QUE OTORGÓ EL CERTIFICADO', '#SPP', 'EMAIL', 'SITIO WEB', 'TELÉFONO');
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:K1');
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10]);
    
    //Se agregan los datos de los alumnos
    $i = 4;
    $contador = 1;
    while ($opp = mysql_fetch_assoc($row_opp)) {
      $fecha = strtotime($opp['fecha_fin']);
      if(!empty($fecha)){
        $vigencia = date('d/m/Y', $fecha);
      }else{
        $vigencia = 'No Disponible';
      }
      $productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }

      $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,  $contador)
                ->setCellValue('B'.$i,  $opp['nombre'])
                ->setCellValue('C'.$i,  $opp['abreviacion'])
                ->setCellValue('D'.$i,  $opp['pais'])
                ->setCellValue('E'.$i,  $productos)
                ->setCellValue('F'.$i,  $vigencia)
                ->setCellValue('G'.$i,  $opp['abreviacion_oc'])
                ->setCellValue('H'.$i,  $opp['spp'])
                ->setCellValue('I'.$i,  $opp['email'])
                ->setCellValue('J'.$i,  $opp['sitio_web'])
                ->setCellValue('K'.$i,  $opp['telefono']);
          $i++;
          $contador++;
    }
    $estiloTituloReporte = array(
          'font' => array(
            'name'      => 'Verdana',
              'bold'      => true,
              'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => '000000'
                  )
            ),
          'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFFFF')
      ),
            'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation'   => 0,
              'wrap'          => TRUE
        )
        );

    $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            'borders' => array(
              'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
      array(
              'font' => array(
                'name'      => 'Arial',               
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/
            'borders' => array(
                'left'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                  'color' => array(
                    'rgb' => '3a2a47'
                    )
                )             
            )
        ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:K".($i-1));
        
    for($i = 'A'; $i <= 'K'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setAutoSize(TRUE);
    }
    
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }
  /***************** LISTA DE EXCEL, ORGANIZACIONES CERTIFICADAS **************************/
  if(isset($_POST['lista_excel']) && $_POST['lista_excel'] == 2){
    $query = $_POST['query_excel'];
    $row_opp = mysql_query($query,$dspp) or die(mysql_error()); 
    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("Reporte OPPs")
               ->setSubject("Reporte OPPs")
               ->setDescription("Reporte OPPs")
               ->setKeywords("")
               ->setCategory("Reporte OPPs");

    $tituloReporte = "LISTA DE ORGANIZACIONES DE PEQUEÑOS PRODUCTORES CERTIFICADAS";
    $titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'OC', 'ULTIMA FECHA DE CERTIFICADO', 'ESTATUS CERTIFICADO', 'RAZÓN SOCIAL', 'DIRECCIÓN FISCAL', 'RFC', 'RUC');
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:K1');
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10]);
    
    //Se agregan los datos de los alumnos
    $i = 4;
    $contador = 1;
    while ($opp = mysql_fetch_assoc($row_opp)) {
      $consultar_socios = mysql_query("SELECT resp1 FROM solicitud_certificacion WHERE idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $socios = mysql_fetch_assoc($consultar_socios);
      $num_socios = '';
      if(isset($socios['resp1'])){
        $num_socios = $socios['resp1'];
      }else{
        $consultar_socios = mysql_query("SELECT numero FROM num_socios WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
        $socios = mysql_fetch_assoc($consultar_socios);
        $num_socios = $socios['numero'];
      }

      $vigencia = '';
      if(isset($opp['vigencia_fin'])){
        $vigencia = $opp['vigencia_fin'];
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);

        if(isset($detalle_certificado['vigencia_fin'])){
          $vigencia = $detalle_certificado['vigencia_fin'];
        }
        
      }

      $vigencia_inicio = '';
      if(isset($opp['vigencia_inicio'])){
        $vigencia_inicio = $opp['vigencia_inicio'];
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_inicio FROM certificado WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);

        if(isset($detalle_certificado['vigencia_inicio'])){
          $vigencia_inicio = $detalle_certificado['vigencia_inicio'];
        }
        
      }

      /*$productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }*/
      $productos = '';
      $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
 
      $estatus_certificado = '';
      if($opp['opp_estatus_opp'] != 'CERTIFICADO' && $opp['opp_estatus_opp'] != 'CANCELADO'){
        $consultar = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = $opp[opp_estatus_opp]", $dspp) or die(mysql_error());
        $detalle = mysql_fetch_assoc($consultar);
        $estatus_certificado = $detalle['nombre'];
      }else{
        $estatus_certificado = $opp['opp_estatus_opp'];
      }




      /*14_11_2017$correos_contactos = '';
      $query_correos = mysql_query("SELECT DISTINCT(email1 SEPARATOR ', ')) AS 'correos_contactos' FROM contactos WHERE email1 != '$opp[email]' AND idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
      $row_correos = mysql_fetch_assoc($query_correos);
      if(isset($row_correos['correos_contactos'])){
        $correos_contactos = $row_correos['correos_contactos'];
      }14_11_2017*/

      $correos_contactos = '';
      $query_contactos = mysql_query("SELECT email1 FROM contactos WHERE idopp = $opp[idopp] GROUP BY email1", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_contactos);
      $cont = 1;
      while($row_contactos = mysql_fetch_assoc($query_contactos)){
        if($cont < $total){
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'].", ";
          }
        }else{
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'];
          }
        }
        $cont++;
      }
      $tipo_solicitud = '';

      $ver_estatus = mysql_query("SELECT estatus_interno FROM opp WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
      $estatus_interno = mysql_fetch_assoc($ver_estatus);
      if($estatus_interno['estatus_interno'] == 11){
        $tipo_solicitud = "SUSPENDIDA";
      }else if($estatus_interno['estatus_interno'] == 12){
        $tipo_solicitud = "INACTIVA";
      }else{
        if($opp['tipo_solicitud'] == 'NUEVA'){
          $tipo_solicitud = 'NUEVA';
        }else if($opp['tipo_solicitud'] == 'RENOVACION'){
          $tipo_solicitud = 'RENOVACION';
        }else{
          $tipo_solicitud = 'NO DISPONIBLE';
        }
      }


      $proceso_solicitud = '';
      if(isset($opp['idsolicitud_certificacion'])){
        $proceso = mysql_query("SELECT idestatus_dspp, nombre FROM estatus_dspp WHERE idestatus_dspp = '$opp[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
        $info_proceso = mysql_fetch_assoc($proceso);

        if($opp['solicitud_estatus_dspp'] == 9){
          $proceso_interno = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$opp[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
          $info_proceso_interno = mysql_fetch_assoc($proceso_interno);
          //13_11_2018echo '('.$opp['solicitud_estatus_dspp'].')'.$info_proceso['nombre'].': <span style="color:green">'.$info_proceso_interno['nombre'].'</span>';
          $proceso_solicitud = mayuscula($info_proceso['nombre']).' - '.mayuscula($info_proceso_interno['nombre']);
          //echo mayuscula($info_proceso['nombre']).': <span style="color:green">'.mayuscula($info_proceso_interno['nombre']).'</span>';
        }else{
          if($info_proceso['idestatus_dspp'] == 12){
            if(file_exists($opp['certificado'])){
              $proceso_solicitud = mayuscula($info_proceso['nombre']);
              //echo '<a href="'.opp['certificado'].'" target="_new"><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span> '.mayuscula($info_proceso['nombre']).'</a>';
            }else{
              $proceso_solicitud = mayuscula($info_proceso['nombre']);
            }
            //13_11_2017echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
          }else{
            //13_11_2018echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
            $proceso_solicitud = mayuscula($info_proceso['nombre']);
          }
        }
      }else{
        $proceso_solicitud = 'SIN SOLICITUD';
      }
      

      /*if($row_opp['estatus_interno'] == 11){
        $tipo_solicitud = "SUSPENDIDA";
      }else{
        $query_tipo = mysql_query("SELECT solicitud_certificacion.tipo_solicitud FROM solicitud_certificacion WHERE idsolicitud_certificacion = '$row_opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
        $tipo_solicitud = mysql_fetch_assoc($query_tipo);
        if($tipo_solicitud['tipo_solicitud'] == 'NUEVA'){
          $tipo_solicitud = 'NUEVA';
        }else if($tipo_solicitud['tipo_solicitud'] == 'RENOVACION'){
          $tipo_solicitud = 'RENOVACIÓN';
        }else{
          $tipo_solicitud = 'NO DISPONIBLE';
        }
      }*/
      array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'OC', 'ULTIMA FECHA DE CERTIFICADO', 'ESTATUS CERTIFICADO', 'RAZÓN SOCIAL', 'DIRECCIÓN FISCAL', 'RFC', 'RUC');

      $query_opp = "SELECT razon_social, direccion_fiscal, rfc, ruc FROM opp WHERE idopp = $opp[idopp]";
      $row_fiscales = mysql_query($query_opp, $dspp) or die(mysql_error());
      $datos_fiscales = mysql_fetch_assoc($row_fiscales);

      $d_fiscales = '';
      if(!empty($datos_fiscales['razon_social'])){
        $d_fiscales .= 'RAZÓN SOCIAL: '.$datos_fiscales['razon_social'];
      }
      if(!empty($datos_fiscales['direccion_fiscal'])){
        $d_fiscales .= 'DIRECCIÓN FISCAL: '.$datos_fiscales['direccion_fiscal'];
      }
      if(!empty($datos_fiscales['rfc'])){
        $d_fiscales .= 'RFC: '.$datos_fiscales['rfc'];
      }
      if(!empty($datos_fiscales['ruc'])){
        $d_fiscales .= 'RUC: '.$datos_fiscales['ruc'];
      }

      $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,  $contador)
                ->setCellValue('B'.$i,  mayuscula($opp['nombre_opp']))
                ->setCellValue('C'.$i,  mayuscula($opp['abreviacion_opp']))
                ->setCellValue('D'.$i,  mayuscula($opp['pais']))
                ->setCellValue('E'.$i, mayuscula($opp['abreviacion_oc']))
                ->setCellValue('F'.$i,  'I: '.$vigencia_inicio.' - F: '.$vigencia)
                ->setCellValue('G'.$i,  mayuscula($estatus_certificado))
                ->setCellValue('H'.$i,  $datos_fiscales['razon_social'])
                ->setCellValue('I'.$i,  $datos_fiscales['direccion_fiscal'])
                ->setCellValue('J'.$i,  $datos_fiscales['rfc'])
                ->setCellValue('K'.$i,  $correos_contactos);

          $i++;
          $contador++;
    }
    $estiloTituloReporte = array(
          'font' => array(
            'name'      => 'Verdana',
              'bold'      => true,
              'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => '000000'
                  )
            ),
          'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFFFF')
      ),
            'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation'   => 0,
              'wrap'          => TRUE
        )
        );

    $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            'borders' => array(
              'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
      array(
              'font' => array(
                'name'      => 'Arial',               
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/
            'borders' => array(
                'left'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                  'color' => array(
                    'rgb' => '3a2a47'
                    )
                )             
            )
        ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:K".($i-1));
        
    /*for($i = 'A'; $i <= 'O'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setAutoSize(TRUE);
    }*/
    
/// AJUSTAR EL TEXTO DE LAS COLUMNAS
    $objPHPExcel->getActiveSheet(0)->getStyle('A1:K1'.$objPHPExcel->getActiveSheet(0)->getHighestRow())
      ->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle('A3:K3')->applyFromArray($estiloTituloColumnas);   
    /// APLICAR TAMAÑO PREDEFINIDO A LAS COLUMNAS
    for($i = 'A'; $i <= 'K'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setWidth(40);
    }


    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

  /***************** LISTA DE EXCEL, ORGANIZACIONES EN PROCESO **************************/
  /***************** LISTA DE EXCEL, ORGANIZACIONES EN PROCESO **************************/
  if(isset($_POST['lista_excel_en_proceso']) && $_POST['lista_excel_en_proceso'] == 2){
    $query = $_POST['query_excel'];
    //echo $query;
    $row_opp = mysql_query($query,$dspp) or die(mysql_error()); 
    // Se crea el objeto PHPExcel

    $objPHPExcel = new PHPExcel();

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("Reporte OPPs")
               ->setSubject("Reporte OPPs")
               ->setDescription("Reporte OPPs")
               ->setKeywords("")
               ->setCategory("Reporte OPPs");

    $tituloReporte = "LISTA DE ORGANIZACIONES DE PEQUEÑOS PRODUCTORES EN PROCESO";
    $titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO', 'PROCESO SOLICITUD', 'ORGANISMO DE CERTIFICACIÓN', '#SPP', 'PASSWORD', 'CORREO ORGANIZACIÓN', 'CORREOS SOLICITUD', 'TELÉFONO ORGANIZACIÓN');
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:L1');
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10])
                ->setCellValue('L3',  $titulosColumnas[11]);
    
    //Se agregan los datos de los alumnos
    $i = 4;
    $contador = 1;
    while ($opp = mysql_fetch_assoc($row_opp)) {
      $vigencia = '';
      if(isset($opp['vigencia_fin'])){
        $vigencia = $opp['vigencia_fin'];
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);

        if(isset($detalle_certificado['vigencia_fin'])){
          $vigencia = $detalle_certificado['vigencia_fin'];
        }
      }

      /*$productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }*/

      /*$query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = 'opp[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
        //echo $productos['lista_productos'];
      }*//*else{
        echo '<p style="color:green">'.$productos['lista_productos'].'</p>';
      }*/
      $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
        //echo $productos['lista_productos'];
      }else{
        //echo '<p style="color:green">'.$productos['lista_productos'].'</p>';
      }

      /*$query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
      }*/

      $estatus_certificado = '';
      /*if($opp['opp_estatus_opp'] != 'CERTIFICADO' && $opp['opp_estatus_opp'] != 'CANCELADO'){
        $consultar = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = $opp[opp_estatus_opp]", $dspp) or die(mysql_error());
        $detalle = mysql_fetch_assoc($consultar);
        $estatus_certificado = $detalle['nombre'];
      }else{
        $estatus_certificado = $opp['opp_estatus_opp'];
      }*/

      /*14_11_2017$correos_contactos = '';
      $query_correos = mysql_query("SELECT DISTINCT(email1 SEPARATOR ', ')) AS 'correos_contactos' FROM contactos WHERE email1 != '$opp[email]' AND idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
      $row_correos = mysql_fetch_assoc($query_correos);
      if(isset($row_correos['correos_contactos'])){
        $correos_contactos = $row_correos['correos_contactos'];
      }14_11_2017*/

      $correos_contactos = '';
      $query_contactos = mysql_query("SELECT email1 FROM contactos WHERE idopp = $opp[idopp] GROUP BY email1", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_contactos);
      $cont = 1;
      while($row_contactos = mysql_fetch_assoc($query_contactos)){
        if($cont < $total){
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'].", ";
          }
        }else{
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'];
          }
        }
        $cont++;
      }
      $proceso_solicitud = '';
      if(isset($opp['idsolicitud_certificacion'])){
        $proceso = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$opp[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
        $info_proceso = mysql_fetch_assoc($proceso);

        if($opp['solicitud_estatus_dspp'] == 9){
          $proceso_interno = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$opp[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
          $info_proceso_interno = mysql_fetch_assoc($proceso_interno);
          //echo $info_proceso['nombre'].': <span style="color:green">'.$info_proceso_interno['nombre'].'</span>';
          $proceso_solicitud = $info_proceso_interno['nombre'];
        }else{
          $proceso_solicitud = $info_proceso['nombre'];
        }
      }else{
        $proceso_solicitud = 'SIN SOLICITUD';
      }

      $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,  $contador)
                ->setCellValue('B'.$i,  mayuscula($opp['nombre_opp']))
                ->setCellValue('C'.$i,  mayuscula($opp['abreviacion_opp']))
                ->setCellValue('D'.$i,  mayuscula($opp['pais']))
                ->setCellValue('E'.$i,  mayuscula($productos['lista_productos']))
                ->setCellValue('F'.$i,  mayuscula($proceso_solicitud))
                ->setCellValue('G'.$i,  mayuscula($opp['abreviacion_oc']))
                ->setCellValue('H'.$i,  $opp['spp'])
                ->setCellValue('I'.$i,  $opp['password'])
                ->setCellValue('J'.$i,  $opp['email'])
                ->setCellValue('K'.$i,  $correos_contactos)
                ->setCellValue('L'.$i,  $opp['telefono']);
          $i++;
          $contador++;
    }
    $estiloTituloReporte = array(
          'font' => array(
            'name'      => 'Verdana',
              'bold'      => true,
              'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => '000000'
                  )
            ),
          'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFFFF')
      ),
            'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation'   => 0,
              'wrap'          => TRUE
        )
        );

    $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            'borders' => array(
              'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
      array(
              'font' => array(
                'name'      => 'Arial',               
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/
            'borders' => array(
                'left'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                  'color' => array(
                    'rgb' => '3a2a47'
                    )
                )             
            )
        ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($estiloTituloColumnas);   
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:L".($i-1));
        
    for($i = 'A'; $i <= 'L'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setAutoSize(TRUE);
    }
    
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }


  /////// LISTA EXCEL ORGANIZACIONES CANCELADAS

  if(isset($_POST['lista_excel_canceladas']) && $_POST['lista_excel_canceladas'] == 2){

    $query = $_POST['query_excel'];
   
   
    $row_opp = mysql_query($query,$dspp) or die(mysql_error());
   
    // Se crea el objeto PHPExcel
    $objPHPExcel = new PHPExcel();
   

    // Se asignan las propiedades del libro
    $objPHPExcel->getProperties()->setCreator("spp global") //Autor
               ->setLastModifiedBy("spp global") //Ultimo usuario que lo modificó
               ->setTitle("Reporte OPPs")
               ->setSubject("Reporte OPPs")
               ->setDescription("Reporte OPPs")
               ->setKeywords("")
               ->setCategory("Reporte OPPs");

    $tituloReporte = "LISTA DE ORGANIZACIONES DE PEQUEÑOS PRODUCTORES CANCELADAS";
    $titulosColumnas = array('Nº', 'NOMBRE DE LA ORGANIZACIÓN', 'ABREVIACIÓN', 'PAÍS', 'PRODUCTO', 'FECHA SIGUIENTE EVALUACIÓN', 'ESTATUS CERTIFICADO', 'ENTIDAD QUE OTORGÓ EL CERTIFICADO', '#SPP', 'PASSWORD', 'CORREO ORGANIZACIÓN', 'CORREOS SOLICITUD', 'TELÉFONO ORGANIZACIÓN');
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells('A1:M1');
            
    // Se agregan los titulos del reporte
    $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A1',$tituloReporte)
                ->setCellValue('A3',  $titulosColumnas[0])
                ->setCellValue('B3',  $titulosColumnas[1])
                ->setCellValue('C3',  $titulosColumnas[2])
                ->setCellValue('D3',  $titulosColumnas[3])
                ->setCellValue('E3',  $titulosColumnas[4])
                ->setCellValue('F3',  $titulosColumnas[5])
                ->setCellValue('G3',  $titulosColumnas[6])
                ->setCellValue('H3',  $titulosColumnas[7])
                ->setCellValue('I3',  $titulosColumnas[8])
                ->setCellValue('J3',  $titulosColumnas[9])
                ->setCellValue('K3',  $titulosColumnas[10])
                ->setCellValue('L3',  $titulosColumnas[11])
                ->setCellValue('M3',  $titulosColumnas[12]);
    
    //Se agregan los datos de los alumnos
    $i = 4;

    $contador = 1;
    while ($opp = mysql_fetch_assoc($row_opp)) {
      $vigencia = '';
      if(isset($opp['vigencia_fin'])){
        $vigencia = $opp['vigencia_fin'];
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);

        if(isset($detalle_certificado['vigencia_fin'])){
          $vigencia = $detalle_certificado['vigencia_fin'];
        }
      }


      /*$productos = '';
      $query_producto = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_producto);
      $cont = 1;
      while($row_producto = mysql_fetch_assoc($query_producto)){
        if($cont < $total){
          $productos .= $row_producto['producto'].", ";
        }else{
          $productos .= $row_producto['producto'];
        }
        $cont++;
      }*/

      $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$opp[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
      }

      $estatus_certificado = '';
      /*if($opp['opp_estatus_opp'] != 'CERTIFICADO' && $opp['opp_estatus_opp'] != 'CANCELADO'){
        $consultar = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = $opp[opp_estatus_opp]", $dspp) or die(mysql_error());
        $detalle = mysql_fetch_assoc($consultar);
        $estatus_certificado = $detalle['nombre'];
      }else{
        $estatus_certificado = $opp['opp_estatus_opp'];
      }*/

      /*14_11_2017$correos_contactos = '';
      $query_correos = mysql_query("SELECT DISTINCT(email1 SEPARATOR ', ')) AS 'correos_contactos' FROM contactos WHERE email1 != '$opp[email]' AND idopp = '$opp[idopp]'", $dspp) or die(mysql_error());
      $row_correos = mysql_fetch_assoc($query_correos);
      if(isset($row_correos['correos_contactos'])){
        $correos_contactos = $row_correos['correos_contactos'];
      }14_11_2017*/

      $correos_contactos = '';
      $query_contactos = mysql_query("SELECT email1 FROM contactos WHERE idopp = $opp[idopp] GROUP BY email1", $dspp) or die(mysql_error());
      $total = mysql_num_rows($query_contactos);
      $cont = 1;
      while($row_contactos = mysql_fetch_assoc($query_contactos)){
        if($cont < $total){
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'].", ";
          }
        }else{
          if($row_contactos['email1'] != $opp['email']){
            $correos_contactos .= $row_contactos['email1'];
          }
        }
        $cont++;
      }

      $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,  $contador)
                ->setCellValue('B'.$i,  mayuscula($opp['nombre_opp']))
                ->setCellValue('C'.$i,  mayuscula($opp['abreviacion_opp']))
                ->setCellValue('D'.$i,  mayuscula($opp['pais']))
                ->setCellValue('E'.$i,  mayuscula($productos['lista_productos']))
                ->setCellValue('F'.$i,  $vigencia)
                ->setCellValue('G'.$i,  'CANCELADO')
                ->setCellValue('H'.$i,  mayuscula($opp['abreviacion_oc']))
                ->setCellValue('I'.$i,  $opp['spp'])
                ->setCellValue('J'.$i,  $opp['password'])
                ->setCellValue('K'.$i,  $opp['email'])
                ->setCellValue('L'.$i,  $correos_contactos)
                ->setCellValue('M'.$i,  $opp['telefono']);
          $i++;
          $contador++;
    }
    $estiloTituloReporte = array(
          'font' => array(
            'name'      => 'Verdana',
              'bold'      => true,
              'italic'    => false,
                'strike'    => false,
                'size' =>16,
                'color'     => array(
                    'rgb' => '000000'
                  )
            ),
          'fill' => array(
        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'FFFFFF')
      ),
            'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_NONE                    
                )
            ), 
            'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'rotation'   => 0,
              'wrap'          => TRUE
        )
        );

    $estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => '#191919'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/

            'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('rgb' => 'B8D186')
      ),
            'borders' => array(
              'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
      'alignment' =>  array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
              'wrap'          => TRUE
        ));

    $estiloInformacion = new PHPExcel_Style();
    $estiloInformacion->applyFromArray(
      array(
              'font' => array(
                'name'      => 'Arial',               
                'color'     => array(
                    'rgb' => '000000'
                )
            ),
            /*'fill'  => array(
        'type'    => PHPExcel_Style_Fill::FILL_SOLID,
        'color'   => array('argb' => 'FFd9b7f4')
      ),*/
            'borders' => array(
                'left'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN ,
                  'color' => array(
                    'rgb' => '3a2a47'
                    )
                )             
            )
        ));

    $objPHPExcel->getActiveSheet()->getStyle('A1:M1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('A3:M3')->applyFromArray($estiloTituloColumnas);   
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:M".($i-1));
        
    for($i = 'A'; $i <= 'M'; $i++){
      $objPHPExcel->setActiveSheetIndex(0)      
        ->getColumnDimension($i)->setAutoSize(TRUE);
    }
    
    // Se asigna el nombre a la hoja
    $objPHPExcel->getActiveSheet()->setTitle('Lista organizaciones');

    // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
    $objPHPExcel->setActiveSheetIndex(0);
    // Inmovilizar paneles 
    //$objPHPExcel->getActiveSheet(0)->freezePane('A4');
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    // Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Lista_organizaciones.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }

 ?>