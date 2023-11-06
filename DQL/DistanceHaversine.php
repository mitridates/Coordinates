<?php

namespace App\Coordinates\DQL;

use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

/**
 * DQL function for calculating distances between two points
 * Example: DISTANCE($fromlat, $fromlng, $tolat, $tolng)
 *             $fn = '( '.  $EarthRadius.' * ACOS(COS(RADIANS(' . $latitude . '))' .
 *              '* COS( RADIANS( '.$alias.'.latitude ) )' .
 *              '* COS( RADIANS( '.$alias.'.longitude )' .
 *              '- RADIANS(' .  $longitude . ') )' .
 *              '+ SIN( RADIANS(' . $latitude . ') )' .
 *              '* SIN( RADIANS( '.$alias.'.latitude ) ) ) )';
 */
class DistanceHaversine extends FunctionNode
{

    /**
     * @var Node
     */
    protected $fromLat;
    /**
     * @var Node
     */
    protected $fromLng;
    /**
     * @var Node
     */
    protected $toLat;
    /**
     * @var Node
     */
    protected $toLng;

    /**
     * @inheritDoc
     * @throws ASTException
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            ' ACOS(COS(RADIANS( %s ))' .
            ' * COS( RADIANS( %s ) )' .
            ' * COS( RADIANS( %s )' .
            ' - RADIANS( %s ) )' .
            ' + SIN( RADIANS( %s ) )' .
            ' * SIN( RADIANS( %s ) ) )',
                $this->toLat->dispatch($sqlWalker),
                $this->fromLat->dispatch($sqlWalker),
                $this->fromLng->dispatch($sqlWalker),
                $this->toLng->dispatch($sqlWalker),
                $this->toLat->dispatch($sqlWalker),
                $this->fromLat->dispatch($sqlWalker)

                );
    }

    /**
     * @inheritDoc
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            $this->fromLat = $parser->ArithmeticPrimary();
            $parser->match(Lexer::T_COMMA);
            $this->fromLng = $parser->ArithmeticPrimary();
            $parser->match(Lexer::T_COMMA);
            $this->toLat = $parser->ArithmeticPrimary();
            $parser->match(Lexer::T_COMMA);
            $this->toLng = $parser->ArithmeticPrimary();
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}