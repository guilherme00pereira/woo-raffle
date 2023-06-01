<?php

namespace UPFlex\MixUp\Core\Parent;

use UPFlex\MixUp\Core\Base;
use UPFlex\MixUp\Core\Interfaces\IParent;
use UPFlex\MixUp\Utils\GroupingType;

abstract class Taxonomy extends Base implements IParent
{
    use GroupingType;

    protected static array $postTypes = [];

    /**
     * @param $instance
     * @return array
     */
    public static function getLabels($instance): array
    {
        return [
            "name" => $instance::$plural,
            "singular_name" => $instance::$singular,
            "menu_name" => $instance::$plural,
            "all_items" => $instance::$male
                ? sprintf(__("Todos os %s"), $instance::$plural)
                : sprintf(__("Todas as %s"), $instance::$plural),
            "edit_item" => sprintf(__("Editar %s"), $instance::$singular),
            "view_item" => sprintf(__("Ver %s"), $instance::$singular),
            "update_item" => $instance::$male
                ? sprintf(__("Atualizar nome do %s"), $instance::$singular)
                : sprintf(__("Atualizar nome da %s"), $instance::$singular),
            "add_new_item" => $instance::$male
                ? sprintf(__("Adicionar novo %s"), $instance::$singular)
                : sprintf(__("Adicionar nova %s"), $instance::$singular),
            "new_item_name" => $instance::$male
                ? sprintf(__("Novo %s"), $instance::$singular)
                : sprintf(__("Nova %s"), $instance::$singular),
            "parent_item" => sprintf(__("%s ascendente"), $instance::$singular),
            "parent_item_colon" => sprintf(__("%s ascendente:"), $instance::$singular),
            "search_items" => sprintf(__("Pesquisar %s"), $instance::$plural),
            "popular_items" => sprintf(__("%s mais populares"), $instance::$plural),
            "separate_items_with_commas" => sprintf(__("Separe %s com vírgulas"), $instance::$plural),
            "add_or_remove_items" => sprintf(__("Adicionar ou excluir %s"), $instance::$plural),
            "choose_from_most_used" => sprintf(
                __("Escolher entre os termos mais usados de %s"),
                $instance::$plural
            ),
            "not_found" => $instance::$male
                ? sprintf(__("Nenhum %s encontrado"), $instance::$singular)
                : sprintf(__("Nenhuma %s encontrada"), $instance::$singular),
            "no_terms" => $instance::$male
                ? sprintf(__("Nenhum %s"), $instance::$singular)
                : sprintf(__("Nenhuma %s"), $instance::$singular),
            "items_list_navigation" => sprintf(__("Navegação na lista de %s"), $instance::$plural),
            "items_list" => sprintf(__("Lista de %s"), $instance::$plural),
            "back_to_items" => sprintf(__("Voltar para %s"), $instance::$plural)
        ];
    }

    public function setPostTypes(array $postTypes): void
    {
        self::$postTypes = $postTypes;
    }

    public static function register(): void
    {
        $class = get_called_class();
        $instance = (new $class);

        $args = [
            "label" => $instance::$plural,
            "labels" => self::getLabels($instance),
            "public" => true,
            "publicly_queryable" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => [
                "slug" => strlen($instance::$slug) > 0 ? $instance::$slug : $instance::$name,
                "with_front" => true
            ],
            "show_admin_column" => false,
            "show_in_rest" => true,
            "show_tagcloud" => false,
            "rest_base" => $instance::$name,
            "rest_controller_class" => "WP_REST_Terms_Controller",
            "show_in_quick_edit" => false,
            "show_in_graphql" => false,
        ];

        register_taxonomy(
            $instance::$name,
            $instance::$postTypes,
            array_merge($args, array_filter($instance::$args))
        );
    }
}
