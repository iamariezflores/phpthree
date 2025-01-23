<?php

namespace iamariezflores\phpthree;

class PhpThree 
{
    public function __construct(protected string $scene_id = "phpthree-scene", protected int $width = 800, 
    protected int $height = 600, protected array $objects = [])
    {}

    public function addObject(string $type, array $params = [], ?string $materialType = null, 
    ?array $materialParams = null, array $position = [0,0,0]): void
    {
        foreach($params as $key => $value) {
            if(!is_numeric($value)) {
                throw new \InvalidArgumentException("PhpTree: Parameter {$key} must be a number.");
            }
        }

        if(count($position) !== 3 || array_filter($position, 'is_numeric') !== $position) {
            throw new \InvalidArgumentException("PhpTree: Position must be an array of 3 numeric values: [x,y,z]");
        }

        $this->objects[] = [
            'type' => $type,
            'params' => $params,
            'materialType' => $materialType,
            'materialParams' => $materialParams,
            'position' => $position,
        ];
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function render()
    {
        return "<div id='{$this->scene_id}' style='width: {$this->width}px; height: {$this->height}px;'>
            {$this->renderScript()}
        </div>";
    }

    public function renderScript(): string
    {
        $objectsScript = array_map(function ($object) {
            $params = implode(',', array_values($object['params']));
            $position = implode(',', $object['position']);
            $material = '';
            $mesh = '';

            if($object['materialType']) {
                $materialParams = json_encode($object['materialParams'] ?? []);
                $material = "const material = new THREE.{$object['materialType']}($materialParams);";
                $mesh = "const obj = new THREE.Mesh(new THREE.{$object['type']}($params), material);";
            } else  {
                $mesh = "const obj = new THREE.{$object['type']}($params);";
            }

            return "$material $mesh obj.position.set($position); scene.add(obj);";
        }, $this->objects);

        $objectsScript = implode("\n", $objectsScript);

        return "
            <script>
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(75, {$this->width} / {$this->height}, 0.1, 1000);
                const light = new THREE.AmbientLight(0x404040);
                const renderer =  new THREE.WebGLRenderer({ alpha: true });
                renderer.setSize({$this->width}, {$this->height});
                document.getElementById('{$this->scene_id}').appendChild(renderer.domElement);
                camera.position.z = 5;

                scene.add(light);

                {$objectsScript}

                function animate() {
                    requestAnimationFrame(animate);

                    scene.rotation.x += 0.01;
                    scene.rotation.y += 0.01;

                    renderer.render(scene, camera);
                }
                animate();
            </script>
        ";
    }
}