<?php

namespace WooRaffles\Admin;

use UPFlex\MixUp\Core\Base;

if (!defined('ABSPATH')) {
    exit;
}

class RaffleMetaBoxes extends Base
{
    public function __construct()
    {
        add_action('acf/init', [$this, 'addFieldGroups']);
    }

    public function addFieldGroups()
    {
        if (function_exists('acf_add_local_field_group')):

            acf_add_local_field_group(
                array(
                    'key' => 'group_raffle_settings',
                    'title' => 'Configurações do Sorteio',
                    'fields' => array(
                        array(
                            'key' => 'field_5fd7d156c1766',
                            'label' => 'Número de cotas',
                            'name' => 'numero_de_cotas',
                            'type' => 'text',
                            'instructions' => 'Quantidade de cotas (números) que serão disponibilizados para venda na sua rifa',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '100',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11,
                        ),
                        array(
                            'key' => 'field_625349ebd1024',
                            'label' => 'Número de dígitos por cota',
                            'name' => 'numero_globos',
                            'type' => 'number',
                            'instructions' => 'Quantidade de dígitos que o campo <b>Número de cotas</b> terá. Ex.: se for 10000, serão 5 casas',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => 3,
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'min' => 2,
                            'max' => 6,
                            'step' => '',
                        ),
                        array(
                            'key' => 'field_5fd7d156c1781',
                            'label' => 'Número máximo de cotas por página',
                            'name' => 'max_por_pagina',
                            'type' => 'text',
                            'instructions' => 'Quantos números irão aparecer de início na rifa (apenas modelo 5 e 6)',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '100',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11,
                        ),
                        array(
                            'key' => 'field_6100c462f0fac',
                            'label' => 'Tipo de cota',
                            'name' => 'tipo_de_cota',
                            'type' => 'select',
                            'instructions' => 'Selecione o tipo de cota (estilo) que sua rifa terá. Funcional apenas no modelo 4 ou inferior',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                'Cota quadrada' => 'Cota quadrada',
                                'Cota redonda' => 'Cota redonda',
                            ),
                            'default_value' => false,
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'return_format' => 'value',
                            'ajax' => 0,
                            'placeholder' => '',
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'product',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                )
            );

            acf_add_local_field_group(
                array(
                    'key' => 'group_open_raffle_styles',
                    'title' => 'Configurações de Cores para Sorteio Cotas Abertas',
                    'fields' => array(
                        array(
                            'key' => 'field_5fd7d126c1767',
                            'label' => 'Cor de fundo ao selecionar cota',
                            'name' => 'cor_de_fundo_selecao',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1768',
                            'label' => 'Cor de fundo cota selecionada modal',
                            'name' => 'cor_de_fundo_selecao_modal_rodape',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1769',
                            'label' => 'Cor de fundo botão finalizar compra',
                            'name' => 'cor_de_fundo_btn_finalizar_compra',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1770',
                            'label' => 'Cor do texto botão finalizar compra',
                            'name' => 'cor_do_texto_btn_finalizar_compra',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1771',
                            'label' => 'Cor da borda da cota antes da seleção',
                            'name' => 'cor_do_borda_cota_antes_selecao',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1772',
                            'label' => 'Cor da borda abas Livres e Reservadas',
                            'name' => 'cor_do_borda_abas_livres_reservadas',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 22
                        ),
                        array(
                            'key' => 'field_5fd7d126c1777',
                            'label' => 'Cor de fundo da reserva indisponível (reservada) no modo de exibição "Todas Juntas"',
                            'name' => 'cor_reserva_juntas',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_5fd7d126c1778',
                            'label' => 'Cor do texto da reserva indisponível (reservada) no modo de exibição "Todas Juntas"',
                            'name' => 'cor_texto_juntas',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_5fd7d126c1757',
                            'label' => 'Cor de fundo da reserva indisponível (rifa presencial) no modo de exibição "Todas Juntas"',
                            'name' => 'cor_reserva_juntas_rifa_presencial',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_5fd7d126c1758',
                            'label' => 'Cor do texto da reserva indisponível (rifa presencial) no modo de exibição "Todas Juntas"',
                            'name' => 'cor_texto_juntas_rifa_presencial',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_5fd7d126c1730',
                            'label' => 'Cor de fundo da reserva comprada no modo de exibição "Todas Juntas"',
                            'name' => 'cor_comprada_juntas',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_5fd7d126c1731',
                            'label' => 'Cor do texto da reserva comprada no modo de exibição "Todas Juntas"',
                            'name' => 'cor_texto_compradas_juntas',
                            'type' => 'color_picker',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '25',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => 11
                        ),
                        array(
                            'key' => 'field_61185ed590977',
                            'label' => 'Cores Modelos TODAS (Modelo 4)',
                            'name' => 'cores_modelos_todas',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_61185eff90978',
                                    'label' => 'Cor de fundo ABA TODAS',
                                    'name' => 'cor_de_fundo_aba_todas',
                                    'type' => 'color_picker',
                                    'instructions' => 'Também será usado na aba participantes',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#dddfde',
                                ),
                                array(
                                    'key' => 'field_61185f1390979',
                                    'label' => 'Cor do texto e borda ABA TODAS',
                                    'name' => 'cor_do_texto_e_borda_aba_todas',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#000000',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_611868bd2ca2c',
                            'label' => 'Cores Modelos LIVRES  (Modelo 4)',
                            'name' => 'cores_modelos_livres',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_611868bd2ca2f',
                                    'label' => 'Cor de fundo ABA LIVRES',
                                    'name' => 'cor_de_fundo_aba_livres',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#ffffff',
                                ),
                                array(
                                    'key' => 'field_611868bd2ca30',
                                    'label' => 'Cor do texto e borda ABA LIVRES',
                                    'name' => 'cor_do_texto_e_borda_aba_livres',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#000000',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_611868c02ca35',
                            'label' => 'Cores Modelos RESERVADAS  (Modelo 4)',
                            'name' => 'cores_modelos_reservadas',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_611868c02ca3a',
                                    'label' => 'Cor de fundo ABA RESERVADAS',
                                    'name' => 'cor_de_fundo_aba_reservadas',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#c6c320',
                                ),
                                array(
                                    'key' => 'field_611868c02ca3b',
                                    'label' => 'Cor do texto e borda ABA RESERVADAS',
                                    'name' => 'cor_do_texto_e_borda_aba_reservadas',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#000000',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_611868c22ca3e',
                            'label' => 'Cores Modelos PAGAS  (Modelo 4)',
                            'name' => 'cores_modelos_pagas',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '50',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_611868c22ca45',
                                    'label' => 'Cor de fundo ABA PAGAS',
                                    'name' => 'cor_de_fundo_aba_pagas',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#48b82f',
                                ),
                                array(
                                    'key' => 'field_611868c22ca46',
                                    'label' => 'Cor do texto e borda ABA PAGAS',
                                    'name' => 'cor_do_texto_e_borda_aba_pagas',
                                    'type' => 'color_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '#000000',
                                ),
                            ),
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'product',
                            ),
                        ),
                    ),
                    'menu_order' => 1,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                )
            );

        endif;
    }
}