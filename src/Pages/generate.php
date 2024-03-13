<?php

/*
if (!in_array('admin',$_SESSION['roles'])){
    $params = [
        'title' => '403 forbidden - access restricted',
        'app_full_url' => get_app_full_url()];

    render('access_denied.html.twig', $params);
    exit();
}

*/


if (isset($_GET['file'])){

    if ($_GET['file'] == 'recu'){
        $id = $_GET['id'];

        $query = new \App\Model\Transactions();
        $result = $query->getRecu($id);
        $params ['result']= $result;
        $fileName = 'Recu_numero_'.$result['reservation_id'];



        $mpdf = new \Mpdf\Mpdf();
        //$mpdf->showImageErrors = true;
        $mpdf->SetAuthor('Hotel Seven');
        $mpdf->SetCreator('Hotel Seven');
        $mpdf->SetCreator('Hotel Seven');
        $mpdf->SetTitle($fileName);


        //$html = '<bookmark content="Start of the Document" /><div>Section 1 text</div>';

        $pdfView = renderPdf('recu_reservation.html.twig',$params);

        $mpdf->SetHTMLFooter('
                <table width="100%">
                    <tr>
                        <td width="80%"><p style="font-size: 10px">Adresse : Avenue Kabinda n°xxxxxxxxx, Commune de Lingwala, Réference Terrain. <br>Tél. +243 XXXXXXXXXXXX</p></td>
                        <td width="20%"><p style="font-size: 10px">Imprimé le {DATE j-m-Y}</p></td>
                    </tr>
                </table>');

        $mpdf->WriteHTML($pdfView);
        $mpdf->Output($fileName,\Mpdf\Output\Destination::DOWNLOAD);


    }

}