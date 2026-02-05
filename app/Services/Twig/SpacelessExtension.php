<?php

namespace App\Services\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TokenParser\TokenParserInterface;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;
use Twig\Attribute\YieldReady;

class SpacelessExtension extends AbstractExtension
{
    public function getTokenParsers(): array
    {
        return [new SpacelessTokenParser()];
    }
}

class SpacelessTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideSpacelessEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new SpacelessNode($body, $lineno);
    }

    public function decideSpacelessEnd(Token $token): bool
    {
        return $token->test('endspaceless');
    }

    public function getTag(): string
    {
        return 'spaceless';
    }
}

#[\Twig\Attribute\YieldReady]
class SpacelessNode extends Node
{
    public function __construct(Node $body, int $lineno)
    {
        parent::__construct(['body' => $body], [], $lineno);
    }

    public function compile(\Twig\Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$content = ob_get_clean();\n")
            ->write("yield preg_replace('/>\\s+</', '><', \$content);\n");
    }
}
