<?php


namespace Hshn\SerializerExtraBundle\Functional;


/**
 * @author Shota Hoshino <lga0503@gmail.com>
 */
class AuthorityTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public static function getConfiguration()
    {
        return 'authority';
    }

    /**
     * @test
     * @dataProvider provideTests
     *
     * @param array $expectedAuthorities
     * @param array $server
     */
    public function testDepth0(array $expectedAuthorities, array $server)
    {
        $client = self::createClient();
        $client->request('GET', '/post/show', [], [], $server);

        $response = $client->getResponse();
        $this->assertEquals($response::HTTP_OK, $response->getStatusCode());

        $post = json_decode($response->getContent(), true);
        $this->assertEquals($expectedAuthorities, $post['_authority']);
    }

    /**
     * @test
     */
    public function provideTests()
    {
        return [
            [['OWNER' => false], []],
            [['OWNER' => true],  ['PHP_AUTH_USER' => 'user1', 'PHP_AUTH_PW' => 'pass1']],
            [['OWNER' => false], ['PHP_AUTH_USER' => 'user2', 'PHP_AUTH_PW' => 'pass2']],
        ];
    }

    /**
     * @test
     */
    public function testDepth1()
    {
        $client = self::createClient();
        $client->request('GET', '/post/list', [], []);

        $response = $client->getResponse();
        $this->assertEquals($response::HTTP_OK, $response->getStatusCode());

        $user = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('posts', $user);
        $this->assertCount(2, $user['posts']);
        foreach ($user['posts'] as $post) {
            $this->assertArrayNotHasKey('_authority', $post);
        }
    }
}
