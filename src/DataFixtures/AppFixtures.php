<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private SluggerInterface $slugger;

    private array $videoLink = [
        'https://www.youtube.com/embed/SQyTWk7OxSI',
        'https://www.youtube.com/embed/Ey5elKTrUCk',
        'https://www.youtube.com/embed/GS9MMT_bNn8',
        'https://www.youtube.com/embed/ogmY2HTr00Q',
        'https://www.youtube.com/embed/CA5bURVJ5zk',
        'https://www.youtube.com/embed/gZFWW4Vus-Q',
    ];

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        SluggerInterface $slugger
    )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = new Factory();
        $faker = $faker::create();

        //------------------------ USER ------------------------//
        $user1 = new User();
        $user1->setUsername('Lolo');
        $user1->setEmail('lolo@gmail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user1, 'lolo');
        $user1->setPassword($hashedPassword);
        $user1->setIsActive(true);
        $user1->setAvatar('user1.png');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('Lucile');
        $user2->setEmail('Lucile@gmail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user2, 'lulu');
        $user2->setPassword($hashedPassword);
        $user2->setIsActive(true);
        $user2->setAvatar('user2.jpeg');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('Gege');
        $user3->setEmail('gege@gmail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user3, 'gege');
        $user3->setPassword($hashedPassword);
        $user3->setIsActive(true);
        $user3->setAvatar('user3.png');
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUsername('Florence');
        $user4->setEmail('flo@gmail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user4, 'floflo');
        $user4->setPassword($hashedPassword);
        $user4->setIsActive(true);
        $user4->setAvatar('user4.jpeg');
        $manager->persist($user4);

        //------------------------ CATEGORY ------------------------//
        $stalls = new Category();
        $stalls->setName('Stalls');
        $manager->persist($stalls);

        $straightAirs = new Category();
        $straightAirs->setName('Straight Airs');
        $manager->persist($straightAirs);

        $grabs = new Category();
        $grabs->setName('Grabs');
        $manager->persist($grabs);

        $ollies = new Category();
        $ollies->setName('Ollies');
        $manager->persist($ollies);

        $flips = new Category();
        $flips->setName('Flips et rotations invers??es');
        $manager->persist($flips);

        $slides = new Category();
        $slides->setName('Slides');
        $manager->persist($slides);

        $tweaks = new Category();
        $tweaks->setName('Tweaks et variations');
        $manager->persist($tweaks);

        $foot = new Category();
        $foot->setName('One foot tricks');
        $manager->persist($foot);

        $autres = new Category();
        $autres->setName('Autres');
        $manager->persist($autres);

        //------------------------ TRICK ------------------------//
        //TRICK 01
        $trick1 = new Trick();
        $trick1->setName('Ollie');
        $trick1->setDescription('Un ollie est une mani??re sp??cifique de ?? sauter ??, de d??coller du sol verticalement en cours de ride. En g??n??ral, nous recommandons d???apprendre le ollie en premier, car il s???agit d???une ??tape cruciale pour l???apprentissage d???autres figures de snowboard. Une fois que vous ma??trisez le ollie, vous pouvez l???utiliser pour les figures sur plat, sur rail et les sauts.');
        $trick1->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick1->getName()));
        $trick1->setSlug($slug);
        $trick1->setCategory($ollies);
        $trick1->setUsers($user1);
        $manager->persist($trick1);

        //TRICK 02
        $trick2 = new Trick();
        $trick2->setName('Press');
        $trick2->setDescription('Un press est l???action d???incliner votre poids sur la spatule (nosepress) ou sur le talon (tailpress) de la planche, de mani??re ?? faire d??coller l???autre extr??mit?? de la planche. Cette figure est vraiment fun et peut ??tre r??alis??e n???importe o?? en montagne, des tremplins et rails jusqu???en plein milieu d???une descente. Variante : Lorsque vous faites l??g??rement pivoter votre press, de sorte que la planche ne pointe pas directement vers la pente, vous r??alisez ce qu???on appelle un ?? butter ??, car cela ressemble au mouvement d???un couteau ??talant du beurre sur une tartine.');
        $trick2->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick2->getName()));
        $trick2->setSlug($slug);
        $trick2->setCategory($autres);
        $trick2->setUsers($user2);
        $manager->persist($trick2);

        //TRICK 03
        $trick3 = new Trick();
        $trick3->setName('50-50');
        $trick3->setDescription('Un 50-50 est lorsque vous glissez sur une box ou un rail (parfois appel?? ?? jib ??) tout en maintenant votre snowboard parall??le au support. La figure de snowboard 50-50 est le moyen parfait de s???habituer ?? la glisse en snowpark, de tester de nouvelles installations et de s?????chauffer. Nous allons d??crire les diff??rentes ??tapes ?? suivre pour rider sur une box, mais ces conseils s???appliquent ??galement aux rails, aux tubes, aux mailboxes et ?? la plupart des jibs.');
        $trick3->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick3->getName()));
        $trick3->setSlug($slug);
        $trick3->setCategory($slides);
        $trick3->setUsers($user1);
        $manager->persist($trick3);

        //TRICK 04
        $trick4 = new Trick();
        $trick4->setName('Tripod');
        $trick4->setDescription('Un tripod est une figure de snowboard tr??s fun qui se r??alise sur terrain plat. Il peut sembler compliqu??, mais s???apprend en r??alit?? plut??t rapidement. La r??alisation du tripod consiste ?? distribuer une partie de votre poids dans la partie sup??rieure de votre corps et dans vos bras, tout en l?????quilibrant sur la spatule ou le talon de votre planche, selon votre pr??f??rence. Nous choisirons ici l???exemple de la spatule.');
        $trick4->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick4->getName()));
        $trick4->setSlug($slug);
        $trick4->setCategory($tweaks);
        $trick4->setUsers($user4);
        $manager->persist($trick4);

        //TRICK 05
        $trick5 = new Trick();
        $trick5->setName('Straight Air');
        $trick5->setDescription('Tout comme le 50-50 sur une box ou un rail, la r??ussite d???un saut d??pend du respect de certains principes : bien vous positionner dans l???axe pour le d??collage, maintenir votre base bien plate ?? l???approche du tremplin, et r??aliser un ollie au moment o?? votre planche quitte le bord. Maintenir votre base ?? plat, vos genoux fl??chis et votre torse bien droit constituent des ??l??ments cl??s pour conserver son ??quilibre lors d???un saut.');
        $trick5->setCreatedAt(new \DateTimeImmutable());
        $trick5->setUpdatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick5->getName()));
        $trick5->setSlug($slug);
        $trick5->setCategory($straightAirs);
        $trick5->setUsers($user3);
        $manager->persist($trick5);

        //TRICK 06
        $trick6 = new Trick();
        $trick6->setName('One Foot');
        $trick6->setDescription('Pour une fois, le nom est assez parlant. Peut-??tre moins impressionnant que les figures d??j?? ??voqu??es, le one foot trick n???en reste pas moins difficile. Comme son nom l???indique, il s???agit de faire un saut et d???atterrir pieds joints.');
        $trick6->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick6->getName()));
        $trick6->setSlug($slug);
        $trick6->setCategory($foot);
        $trick6->setUsers($user4);
        $manager->persist($trick6);

        //TRICK 07
        $trick7 = new Trick();
        $trick7->setName('Frontside 720');
        $trick7->setDescription('Ben Ferguson, le snowboarder am??ricain est connu pour les figures impressionnantes qu???il arrive ?? r??aliser en comp??tition, pour sa participation aux JO de 2018 et ces m??dailles d???argent et de bronze aux Winter X Games, une comp??tition de swnoboard am??ricaine. Il s???agit d???un saut ?? 720?? ou on tourne dans l???air en vrillant.');
        $trick7->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick7->getName()));
        $trick7->setSlug($slug);
        $trick7->setCategory($stalls);
        $trick7->setUsers($user1);
        $manager->persist($trick7);

        //TRICK 08
        $trick8 = new Trick();
        $trick8->setName('Triple Cork');
        $trick8->setDescription('Aux derniers Jeux de P??kin, le Japonais Ayumu Hirano a enfin r??ussi a d??coch?? l???or avec une figure incroyable et tr??s difficile ?? r??aliser en comp??tition : un triple cork. Ayumu Hirano ??tait arriv?? deux fois deuxi??me, et cette fois-ci il est enfin parvenu ?? monter sur la plus haute marche du podium. Et pour vous expliquer un peu, un triple cork est un saut avec des rotations diagonales, plus on en fait plus le saut est impressionnant.');
        $trick8->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick8->getName()));
        $trick8->setSlug($slug);
        $trick8->setCategory($flips);
        $trick8->setUsers($user2);
        $manager->persist($trick8);

        //TRICK 09
        $trick9 = new Trick();
        $trick9->setName('Backside 2160');
        $trick9->setDescription('Le 8 avril, le Japonais Hiroto Ogiwara est rentr?? dans l???histoire du snowboard en r??alisant ?? seulement 16 ans un ?? Backside 2160 ?? (figure r??alis??e seulement au ski auparavant), une figure qui consiste ?? faire 6 rotations sur soi-m??me.');
        $trick9->setCreatedAt(new \DateTimeImmutable());
        $trick9->setUpdatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick9->getName()));
        $trick9->setSlug($slug);
        $trick9->setCategory($flips);
        $trick9->setUsers($user3);
        $manager->persist($trick9);

        //TRICK 10
        $trick10 = new Trick();
        $trick10->setName('Mc Twist');
        $trick10->setDescription('Un grand classique des rotations t??te en bas qui se fait en backside, sur un mur backside de pipe. Le Mc Twist est g??n??ralement fait en japan, un grab tr??s tweak??.');
        $trick10->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick10->getName()));
        $trick10->setSlug($slug);
        $trick10->setCategory($tweaks);
        $trick10->setUsers($user1);
        $manager->persist($trick10);

        //TRICK 11
        $trick11 = new Trick();
        $trick11->setName('Cork');
        $trick11->setDescription('Le diminutif de corkscrew qui signifie litt??ralement tire-bouchon et d??signait les premi??res simples rotations t??tes en bas en frontside. D??sormais, on utilise le mot cork ?? toute les sauces pour qualifier les figures o?? le rider passe la t??te en bas, peu importe le sens de rotation. Et dor??navant en comp??tition, on parle souvent de double cork, triple cork et certains riders vont jusqu\'au quadruple cork !');
        $trick11->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick11->getName()));
        $trick11->setSlug($slug);
        $trick11->setCategory($flips);
        $trick11->setUsers($user2);
        $manager->persist($trick11);

        //TRICK 12
        $trick12 = new Trick();
        $trick12->setName('Handplant');
        $trick12->setDescription('Un trick inspir?? du skate qui consiste ?? tenir en ??quilibre sur une ou deux mains au sommet d\'une courbe. Existe avec de nombreuses variantes dans les grabs et les rotations.');
        $trick12->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick12->getName()));
        $trick12->setSlug($slug);
        $trick12->setCategory($autres);
        $trick12->setUsers($user1);
        $manager->persist($trick12);

        //TRICK 13
        $trick13 = new Trick();
        $trick13->setName('Backside air');
        $trick13->setDescription('Le grab star du snowboard qui peut ??tre fait d\'autant de fa??on diff??rentes qu\'il y a de styles de riders. Il consiste ?? attraper la carre arri??re entre les pieds, ou l??g??rement devant, et ?? pousser avec sa jambe arri??re pour ramener la planche devant. C\'est une figure phare en pipe ou sur un hip en backside. C\'est g??n??ralement avec ce trick que les riders vont le plus haut.');
        $trick13->setCreatedAt(new \DateTimeImmutable());
        $trick13->setUpdatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick13->getName()));
        $trick13->setSlug($slug);
        $trick13->setCategory($flips);
        $trick13->setUsers($user4);
        $manager->persist($trick13);

        //TRICK 14
        $trick14 = new Trick();
        $trick14->setName('Switch');
        $trick14->setDescription('Le grab star du snowboard qui peut ??tre fait d\'autant de fa??on diff??rentes qu\'il y a de styles de riders. Il consiste ?? attraper la carre arri??re entre les pieds, ou l??g??rement devant, et ?? pousser avec sa jambe arri??re pour ramener la planche devant. C\'est une figure phare en pipe ou sur un hip en backside. C\'est g??n??ralement avec ce trick que les riders vont le plus haut.');
        $trick14->setCreatedAt(new \DateTimeImmutable());
        $slug = strtolower($this->slugger->slug($trick14->getName()));
        $trick14->setSlug($slug);
        $trick14->setCategory($autres);
        $trick14->setUsers($user1);
        $manager->persist($trick14);

        //------------------------ IMAGE ------------------------//
        for ($i=1; $i<15; $i++) {
            $image = new Image();
            $image->setName('img'.$i.'.jpg');
            $rand = rand(1,14);
            $trick = ${'trick'.$rand};
            $image->setTricks($trick);
            $manager->persist($image);
        }

        for ($i=1; $i<15; $i++) {
            $featuredImage = new Image();
            $j = $i + 14;
            $featuredImage->setName('img'.$j.'.jpg');
            $trick = ${'trick'.$i};
            $featuredImage->setTricks($trick);
            $featuredImage->setfeaturedImage(true);
            $manager->persist($featuredImage);
        }

        //------------------------ VIDEO ------------------------//
        for ($i=0; $i<6; $i++){
            for ($j = 1; $j < 5; $j++) {
                $video = new Video();
                $video->setLink($this->videoLink[$i]);
                $rand = rand(1,14);
                $trick = ${'trick'.$rand};
                $video->setTricks($trick);
                $manager->persist($video);
            }
        }

        //------------------------ COMMENT ------------------------//
        for ($i=0; $i<60; $i++){
            $randTrick = rand(1,14);
            $randUser = rand(1,4);
            $randContent = rand(50, 200);
            $comment = new Comment();
            $comment->setUsers(${'user'.$randUser});
            $comment->setTricks(${'trick'.$randTrick});
            $comment->setContent($faker->text($randContent));
            $comment->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
