(function ($) {

    let cotasModelo = {};
    let impressosPagina = 1;
    let livresImpressos = 0;
    let cotasSelecionadas = [];

    $(document).ready(function () {
        retornaCotas()
    })

    $('.btn-carregar-mais-numeros').click(function (e) {
        imprimirNumeros();
    })

    $('body').on('change', 'input[name="cotas"]', function (e) {
        selecionarCotaRifa(this.value)
    })

    function imprimirNumeros() {

        let h = 1;
        let i = 0;
        const j = impressosPagina;

        const exibir_nome_do_comprador_ou_texto_padrao = cotasModelo.exibir_nome_do_comprador_ou_texto_padrao;
        const texto_tooltip_reserva_presencial = cotasModelo.texto_tooltip_reserva_presencial;
        const texto_tooltip_reserva = cotasModelo.texto_tooltip_reserva;
        const texto_tooltip_comprado = cotasModelo.texto_tooltip_comprado;
        let max_por_pagina = cotasModelo.max_por_pagina;

        if (max_por_pagina === "" || max_por_pagina === null || max_por_pagina === 0) {
            max_por_pagina = 100;
        }

        const porPagina = max_por_pagina;

        if (j >= cotasModelo.cotas) {
            $(".btn-carregar-mais-numeros").hide();
        }

        while (h <= porPagina) {
            i = impressosPagina;
            if (j < cotasModelo.cotas) {
                // TAB 1 (TODAS))
                $("#iteneRifaAba0").append(`
                    <div class="form-check conteme1" data-cota="${coloque_zero(i, cotasModelo.globos)}" id="fcTodos${coloque_zero(i, cotasModelo.globos)}">
                          <input class="form-check-input" type="checkbox" name="cotas"
                            value="${coloque_zero(i, cotasModelo.globos)}" id="cota${coloque_zero(i, cotasModelo.globos)}">
                          <label class="form-check-label label-um" for="cota${coloque_zero(i, cotasModelo.globos)}">
                            ${coloque_zero(i, cotasModelo.globos)}
                          </label>
                    </div>
                  `);

                // TAB 3 (RESERVADAS)
                $("#iteneRifaAba2").append(`
                      <div class="form-check modelo-4-reservada me-de-tooltip conteme2" 
                          data-cota="${coloque_zero(i, cotasModelo.globos)}" style="" id="fcr${coloque_zero(i, cotasModelo.globos)}" 
                          aria-label="${texto_tooltip_reserva}" data-texto-reserva="${texto_tooltip_reserva}" 
                          data-texto-presencial="${texto_tooltip_reserva_presencial}">
                            <input disabled="" class="form-check-input" type="checkbox" name="cotas" 
                                value="${coloque_zero(i, cotasModelo.globos)}" id="cotar${coloque_zero(i, cotasModelo.globos)}">
                            <label class="form-check-label  label-um" for="cotar${coloque_zero(i, cotasModelo.globos)}">
                                 ${coloque_zero(i, cotasModelo.globos)}
                            </label>
                      </div>
                   `);


                // TAB 4 (PAGAS)
                $("#iteneRifaAba3").append(`
                        <div class="form-check modelo-4-comprada me-de-tooltip conteme3" 
                            data-cota="${coloque_zero(i, cotasModelo.globos)}" style="" 
                            id="fcc${coloque_zero(i, cotasModelo.globos)}" aria-label="Nome comprador" 
                            data-texto-comprado="${texto_tooltip_comprado}">
                            <input disabled="" class="form-check-input" type="checkbox" name="cotas" 
                                value="${coloque_zero(i, cotasModelo.globos)}" id="cotac${coloque_zero(i, cotasModelo.globos)}">
                            <label class=" form-check-label label-um" for="cotac${coloque_zero(i, cotasModelo.globos)}">
                                ${coloque_zero(i, cotasModelo.globos)}
                            </label>
                        </div>
                   `);
                // ATUALIZAR O CONTADOR DE NUMEROS IMPRESSOS NA TELA
                impressosPagina++;
            } // FIM DO IF DE VERIFICAÇÃO DAS COTAS
            h++;
        } // FIM DO WHILTE PRINCIPAL


        // REMOVER DAS ABAS RESERVADAS E PAGAS,
        // OS NUMEROS QUE ESTAO LIVRES
        // IMPRIMIR OS NUMEROS QUE ESTAO LIVRES
        let n = 0;
        let m = 0;


        while (n < cotasModelo.livres.length) {

            $(`#fcr${cotasModelo.livres[n]}`).remove();
            $(`#fcc${cotasModelo.livres[n]}`).remove();

            if (m <= porPagina) {
                if (n >= livresImpressos) {


                    if ($(`#fcTodos${cotasModelo.livres[n]}`).length === 0) {

                        $("#iteneRifaAba0").append(`
                                <div class="form-check conteme1" data-cota="${cotasModelo.livres[n]}" id="fcTodos${cotasModelo.livres[n]}">
                                      <input class="form-check-input" type="checkbox" name="cotas" 
                                        value="${cotasModelo.livres[n]}" id="cota${cotasModelo.livres[n]}">
                                      <label class="form-check-label label-um" for="cota${cotasModelo.livres[n]}">
                                          ${cotasModelo.livres[n]}
                                      </label>
                                </div>
                      `);

                    }


                    $("#iteneRifaAba1").append(`
                      <div class="form-check conteme1" data-cota="${cotasModelo.livres[n]}" id="fcClone${cotasModelo.livres[n]}">
                              <input class="form-check-input" type="checkbox" name="cotasClone" 
                                value="${cotasModelo.livres[n]}" id="cotaClone${cotasModelo.livres[n]}">
                              <label class="form-check-label label-um" for="cotaClone${cotasModelo.livres[n]}">
                                ${cotasModelo.livres[n]}
                              </label>
                      </div>
                `);
                    m++;
                    livresImpressos++;

                }
            }
            n++;
        }// FINAL DO WHIlE


        // WHILE DA CORREÇÃO DAS CORRESPONDENCIAS
        try {

            let m = 0;
            while (m < cotasModelo.participantes.length) {

                if (cotasModelo.participantes[m].cotas != null && cotasModelo.participantes[m].cotas !== undefined && cotasModelo.participantes[m].cotas !== "") {

                    const regAtual = cotasModelo.participantes[m].cotas.split(",");

                    // REMOVR RESERVADAS E PAGAS DAS COTAS LIVRES
                    if (cotasModelo.participantes[m].status === "processing" ||
                        cotasModelo.participantes[m].status === "completed" ||
                        cotasModelo.participantes[m].status === "on-hold" ||
                        cotasModelo.participantes[m].status === "pending" ||
                        cotasModelo.participantes[m].status === "rifa-presencial") {

                        let c1 = 0;
                        while (c1 < regAtual.length) {

                            $(`#fcClone${regAtual[c1]}`).remove();
                            c1++;
                        }

                    }// FINAL LIMPEZA LIVRES

                    // REMOVER AS PAGAS E LIVRES DA ABA DE PAGAS
                    if (cotasModelo.participantes[m].status === "processing" ||
                        cotasModelo.participantes[m].status === "completed") {

                        let c2 = 0;
                        while (c2 < regAtual.length) {

                            $(`#fcr${regAtual[c2]}`).remove();

                            // AJUSTE DA ABA TODOS
                            $(`#fcTodos${regAtual[c2]}`).addClass("modelo-4-comprada");
                            $(`#fcTodos${regAtual[c2]}`).addClass("me-de-tooltip");

                            $(`#fcTodos${regAtual[c2]} input`).attr("disabled", true);

                            if (exibir_nome_do_comprador_ou_texto_padrao == "Nome comprador") {
                                $(`#fcc${regAtual[c2]}`).attr("aria-label", cotasModelo.participantes[m].nome);
                                $(`#fcTodos${regAtual[c2]}`).attr("aria-label", cotasModelo.participantes[m].nome);
                            } else {
                                $(`#fcc${regAtual[c2]}`).attr("aria-label", $(`#fcc${regAtual[c2]}`).attr("data-texto-comprado"));
                                $(`#fcTodos${regAtual[c2]}`).attr("aria-label", $(`#fcc${regAtual[c2]}`).attr("data-texto-comprado"));
                            }
                            c2++;

                        }

                    }// FINAL LIMPEZA PAGAS


                    // REMOVER AS RESERVADAS DA ABA RESERVADAS
                    if (cotasModelo.participantes[m].status === "on-hold" ||
                        cotasModelo.participantes[m].status === "pending" ||
                        cotasModelo.participantes[m].status === "rifa-presencial") {

                        let c3 = 0;
                        while (c3 < regAtual.length) {

                            $(`#fcc${regAtual[c3]}`).remove();

                            // AJUSTE DA ABA TODOS
                            $(`#fcTodos${regAtual[c3]}`).addClass("modelo-4-reservada");
                            $(`#fcTodos${regAtual[c3]}`).addClass("me-de-tooltip");

                            $(`#fcTodos${regAtual[c3]} input`).attr("disabled", true);

                            // TOOLTP GERAL DAS RESERVAS
                            $(`#fcTodos${regAtual[c3]}`).attr("aria-label", $(`#fcr${regAtual[c3]}`).attr("data-texto-reserva"));

                            // AJUSTAR TEXTO TOOLTIP SE FOR PRESENCIAL
                            if (cotasModelo.participantes[m].status === "rifa-presencial") {
                                $(`#fcr${regAtual[c3]}`).attr("aria-label", texto_tooltip_reserva_presencial);
                                $(`#fcTodos${regAtual[c3]}`).attr("aria-label", texto_tooltip_reserva_presencial);
                            }

                            c3++;

                        }

                    }// FINAL LIMPEZA RESERVADAS

                }// FIM DO IF DE VERIFICAÇÃO DE COTAS (SE ESTA EM BRNACO OU NAO)

                m++;
            }

        } catch (e) {
            console.log("NENHUM PARTICIPANTE AINDA");
        }


        // REOEDENAR OS ITENS
        $('#iteneRifaAba0').find('.form-check').sort(function (a, b) {
            return $(a).attr('data-cota') - $(b).attr('data-cota');
        })
            .appendTo('#iteneRifaAba0');

        $('[id]').each(function (i) {
            $('[id="' + this.id + '"]').slice(1).remove();
        });


    }

    function retornaCotas() {
        const totalTodos = $("#totalTodos");
        const totalL = $("#totalL");
        const totalR = $("#totalR");
        const totalC = $("#totalC");

        const params = {
            action: ajaxobj.action_ajaxApiRifaInfos,
            nonce: ajaxobj.raffle_nonce,
            rifa: ajaxobj.productId
        }
        $.get(ajaxobj.ajax_url, params, function (res) {
            cotasModelo = res;
            let totalPagas = 0;
            let totalReservas = 0;

            try {
                for (let i = 0; i < cotasModelo.participantes.length; i++) {
                    let cotasPagas = []
                   if(cotasModelo.participantes[i].cotas !== "" && cotasModelo.participantes[i].cotas !== null && cotasModelo.participantes[i].cotas !== undefined){
                       cotasModelo.participantes[i].cotas.split(",");
                   }
                   console.log(cotasPagas);

                    if (cotasModelo.participantes[i].status === "processing" || cotasModelo.participantes[i].status === "completed") {

                        totalPagas = totalPagas + cotasPagas.length - 1;
                    }

                    if (cotasModelo.participantes[i].status === "on-hold" || cotasModelo.participantes[i].status === "pending" || cotasModelo.participantes[i].status === "rifa-presencial") {
                        totalReservas = totalReservas + cotasPagas.length - 1;
                    }

                    // ALIMENTAR OS CONTADORES DAS ABAS
                    totalTodos.html(cotasModelo.cotas); // TODAS
                    totalL.html(cotasModelo.livres.length); // LIVRES
                    totalR.html(totalReservas); // RESERVADAS
                    totalC.html(cotasModelo.cotas - cotasModelo.livres.length - totalReservas); // PAGAS

                    setTimeout(function () {
                        const conteme3len = $(".modelo-4-comprada.conteme3").length;
                        if (cotasModelo.cotas <= conteme3len) {
                            totalC.html(conteme3len);
                        }

                    }, 6000);

                }// FINAL FOR

                totalTodos.html(cotasModelo.cotas); // TODAS
                totalL.html(cotasModelo.livres.length); // LIVRES

            } catch (e) {

                // ALIMENTAR OS CONTADORES DAS ABAS
                totalTodos.html(cotasModelo.cotas); // TODAS
                totalL.html(cotasModelo.livres.length); // LIVRES
                totalR.html(0); // RESERVADAS
                totalC.html(0); // PAGAS

            }
            // FAZER A PRIMEIRA IMPRESSÂO
            imprimirNumeros();
        }, 'json');
    }


    function coloque_zero(input0, globos) {
        return input0.toString().padStart(globos, "0");
    }

    function selecionarCotaRifa(numeroCota) {
        if ($(`#cota${numeroCota}:checked`).length > 0) {
            $(`#cotaClone${numeroCota}`).prop('checked', true);
        } else {
            $(`#cotaClone${numeroCota}`).prop('checked', false);
        }
        cotasSelecionadas.push(numeroCota);
        let idDoProduto = document.getElementById("idDoProdutoInput").value;
        salvarCarrinho(idDoProduto, cotasSelecionadas.length);
    }

    function removerSelecaoCota(cotaValue){
        let cota = document.getElementById("cotaSpan"+cotaValue);
        cota.remove();
        document.getElementById("cota"+cotaValue).checked = false;
        setTimeout(function(){ document.getElementById("cotaClone"+cotaValue).checked = false; }, 1500);
    }

    function salvarCarrinho(idProduto, quantidade) {
        const params = {
            action: ajaxobj.action_ajaxAddtoCart,
            nonce: ajaxobj.raffle_nonce,
            produto: idProduto,
            quantidade: quantidade
        }
        $.get(ajaxobj.ajax_url, params, function (res) {
            console.log(res);
        });
    }

}(jQuery))
