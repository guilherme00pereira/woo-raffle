# MixUp

![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![WordPress](https://img.shields.io/badge/WordPress-%23117AC9.svg?style=for-the-badge&logo=WordPress&logoColor=white)

> O MixUp foi criado para facilitar a escrita e organização de classes, e scripts relacionados a elas, em projetos baseados no Wordpress.<br/>
> Utilizando as melhores práticas de OOP, um dos principais pilares do projeto é entregar uma estrutura de código testável e alinhado com os padrões de engenharia de software.

## 📑 Índice

- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Exemplos](#-exemplos)
- [Sugestão](#-sugestão)
- [Contribuição](#-contribuição)

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:
- [x] Instalou o `PHP v7.4+`
- [x] Instalou o [`WordPress`](https://br.wordpress.org/download/)
- [x] Configurou o `composer.json` conforme o [guia](#-instalação) nesse arquivo

## 📂 Instalação

Para instalar o MixUp, siga essas etapas:

1 - Acesse a pasta do seu plugin / tema do Wordpress e rode o comando:
```
cd wp-content/plugins/meu-plugin
composer install upflex/mixup
```
2 - Acesse o painel do Wordpress e verifique se o plugin / tema está ativo.

## ⚙️ Configuração

Após ativação, a biblioteca estará pronta para uso nos seus temas e plugins.<br/>
Exemplo básico de um arquivo `functions.php` ou `meu-plugin.php`:
```
<?php
use UPFlex\MixUp\Core\Base;
use UPFlex\MixUp\Core\Instance\Create;

# Valida autoload
require_once __DIR__ . '/vendor/autoload.php';

# Admin
Create::run(
    'BaseWp\Admin', # Namespace para instância automática das classes
    Base::class, # Classe base
    [], # Parâmetros adicionais
    get_stylesheet_directory(__FILE__) # Local do arquivo composer.json 
);
```

Lembre-se de adicionar no arquivo `composer.json`, dos seus temas e plugins, os parâmetros para o autoload:
```
"autoload": {
    "psr-4": {
      "TemaWp\\Admin\\": "admin/classes/",
      "TemaWp\\Front\\": "front/classes/"
    }
  }
```

**Observação:** _A classe **Instance\Create** utiliza os parâmetros do `composer.json` para instância automática._<br/>

## 🗺️ Exemplos

Segue abaixo uma pequena lista de exemplos de uso:

> **Action / Filter**: criação de um novo action / filter com auto-instância.
>    ```
>    <?php
>
>    namespace BaseWp\Front;
>    
>    use UPFlex\MixUp\Core\Base;
>    
>    class Assets extends Base
>    {
>        public function __construct()
>        {
>            add_action('wp_enqueue_scripts', [self::class, 'styles']);
>            add_filter('meu_plugin_priority', [self::class, 'getPriority']);
>        }
> 
>        public static function getPriority() : int
>        {
>           return 10;
>        }
>        
>        public static function styles()
>        {
>            wp_enqueue_style('meu_plugin', 'style.css');
>        }
>    }
>    ```

> **Higienização de campos**: higienização dos parâmetros GET / POST.
>    ```
>    <?php
>
>    namespace BaseWp\Front;
>    
>    use UPFlex\MixUp\Core\Base;
>    use UPFlex\MixUp\Utils\Fields\Sanitize;
>    
>    class Contact extends Base
>    {
>        use Sanitize;
>    
>        public function __construct()
>        {
>            add_action('wp_ajax_meu_plugin_send_message', [self::class, 'send']);
>            add_action('wp_ajax_nopriv_meu_plugin_send_message', [self::class, 'send']);
>        }
>    
>        public function send() 
>        {
>            $fields = self::getFields('get'); # Recupera campos $_GET
>    
>            if(!empty($fields['my_email'])) {
>                return wp_send_json_success();
>            }
>    
>            return wp_send_json_error();
>        }
>        
>        # Método obrigatório    
>        protected static function setFields(): array
>        {
>            # Informa os campos e seus tipos
>            return [
>               'name',
>               'my_email' => 'email',
>            ];
>       }
>    }
>    ```

> **PostType**: criação de um novo tipo de post.
>    ```
>    <?php
>
>    namespace BaseWp\Admin;
>    
>    use UPFlex\MixUp\Core\Parent\PostType;
>    
>    class Events extends PostType
>    {
>        protected static string $name = 'events';
>    
>        public function __construct()
>        {
>            self::setIcon('dashicons-groups');
>            self::setPlural('Eventos');
>            self::setSingular('Evento');
>            self::setSlug('eventos');
>            self::setArgs([]); # Opcional
>    
>            add_action('init', [self::class, 'register']);
>        }
>    }
>    ```

> **Shortcode**: criação de um novo shortcode.
>    ```
>    <?php
>
>    namespace BaseWp\Admin;
>    
>    use UPFlex\MixUp\Core\Parent\Shortcode;
>    
>    class ListEvents extends Shortcode
>    {    
>        public function __construct()
>        {
>            self::setTag('meu_plugin_list_events');
>            self::setCallback([self::class, 'render']);
>        }
>        
>        public static function render()
>        {
>        }
>    }
>    ```

> **Taxonomia**: criação de uma nova taxonomia.
>    ```
>    <?php
>
>    namespace BaseWp\Admin;
>    
>    use UPFlex\MixUp\Core\Parent\Taxonomy;
>    
>    class EventTypes extends Taxonomy
>    {
>        protected static string $name = 'event_types';
>    
>        public function __construct()
>        {
>            self::setPlural('Tipos');
>            self::setSingular('Tipo');
>            self::setSlug('tipos-de-evento');
>            self::setSlug('eventos');
>            self::setPostTypes([Events::getName()]); # Pode ser uma string
>    
>            add_action('init', [self::class, 'register']);
>        }
>    }
>    ```

> **Validação de campos**: validação e higienização dos parâmetros GET / POST:
>    ```
>    <?php
>
>    namespace BaseWp\Front;
>    
>    use UPFlex\MixUp\Core\Base;
>    use UPFlex\MixUp\Utils\Fields\Validate;
>    
>    class Contact extends Base
>    {
>        use Validate;
>    
>        public function __construct()
>        {
>            add_action('wp_ajax_meu_plugin_send_message', [self::class, 'send']);
>            add_action('wp_ajax_nopriv_meu_plugin_send_message', [self::class, 'send']);
>        }
>    
>        public function send()
>        {
>            $fields = self::getFieldsValidated('get'); # Recupera campos $_GET
>            $validate = $fields['success'] ?? true;
>    
>            if($validate) {
>                return wp_send_json_success();
>            }
>    
>            return wp_send_json_error();
>        }
>        
>        # Método obrigatório
>        protected static function setFields(): array
>        {
>            # Informa os campos e validações necessárias
>            return [
>               'name' => 'required|min:1|max:20',
>               'email' => 'required',
>            ];
>       }
>    }
>    ```

## 📫 Contribuição
Para contribuir com o projeto, siga estas etapas:

1. Clone este repositório.
2. Crie um branch: `git checkout -b <nome_branch>`.
3. Faça suas alterações e confirme-as: `git commit -m '<mensagem_commit>'`
4. Envie para o branch original: `git push origin <nome_branch>`
5. Crie a solicitação de pull request.

Como alternativa, consulte a documentação do GitHub em [como criar uma solicitação pull](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).

## 🎓 Informações técnicas para contribuição

O plugin possui alguns componentes e estruturas essenciais para uso nas classes.
Segue abaixo uma pequena lista:

> **Core**: são classes e interfaces que fazem parte da estrutura principal do plugin.
> * **Base**: classe responsável pela instância automática das classes filhas.
> * **Instance**: são classes responsáveis pela instância automática, predefinida na classe **Base**.
>  * **Create**: responsável pela criação da instância da classe.
>  * **Finder**: responsável pela localização das classes, definidas no arquivo principal do tema/plugin
     e indexando com o arquivo `composer.json`.
>
>
> * **Interfaces**: são interfaces responsáveis pela predefinição das classes que as utilizam.
>  * **IParent**: responsável pela predefinição das classes que criam os tipos customizados de posts e taxonomias.
>
>
> * **Parent**: são classes que organizam funções nativas do WordPress.
>  * **PostType**: responsável pela criação de tipos customizados de posts.
>  * **Shortcode**: responsável pela criação de shortcodes.
>  * **Taxonomy**: responsável pela criação das taxonomias.

> **Utils**: são traits que podem ser adicionados as suas classes para melhor aproveitamento e legibilidade
do seu código.
> * **Fields**: responsável pelo tratamento e uso de campos.
>  * **Sanitize**: responsável pela sanitização dos parâmetros GET e POST, conforme no definição:
>
>         protected static function setFields(): array
>  * **Validate**: responsável pela validação dos campos, conforme definição no método:
>
>         protected static function setFields(): array
>
>
>  * **Email**: responsável pela definição do template e envio de e-mail.
>  * **GroupingType**: responsável pela definição do uso em taxonomias e tipos customizados de post.
     O método abaixo deve ser sempre adicionado nas classes:
>
>          public static function register(): void
>  * **Message**: responsável pela definição do envio de mensagem, junto com o envio de e-mail.
     O método abaixo deve ser sempre adicionado nas classes:
>
>           protected static function send(): array
>  * **Response**: responsável pelo retorno de informações ao usuário ou outras classes.
>  * **TemplateParts**: responsável pela criação de template-parts nos plugins, seguindo a ideia do
     `get_template_parts()` nativo para temas.

## 😄 Seja um dos contribuidores<br>

Quer fazer parte desse projeto? Siga o guia de contribuição acima.

[⬆ &nbsp; Voltar ao topo](#-contribuição)
