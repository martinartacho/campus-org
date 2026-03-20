<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Documentació',
                'slug' => 'documentacio',
                'description' => 'Documentació general del campus',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Secretaria',
                        'slug' => 'secretaria',
                        'description' => 'Documents de la secretaria',
                        'sort_order' => 1,
                        'children' => [
                            [
                                'name' => 'Actes',
                                'slug' => 'actes',
                                'description' => 'Actes de reunions i juntes',
                                'sort_order' => 1,
                            ],
                            [
                                'name' => 'Correspondència',
                                'slug' => 'correspondencia',
                                'description' => 'Correspondència oficial',
                                'sort_order' => 2,
                                'children' => [
                                    [
                                        'name' => 'Ajuntament',
                                        'slug' => 'ajuntament',
                                        'description' => 'Documents de l\'ajuntament',
                                        'sort_order' => 1,
                                    ],
                                    [
                                        'name' => 'Agència Tributària',
                                        'slug' => 'agencia-tributaria',
                                        'description' => 'Documents de l\'agència tributària',
                                        'sort_order' => 2,
                                    ],
                                    [
                                        'name' => 'Generalitat',
                                        'slug' => 'generalitat',
                                        'description' => 'Documents de la Generalitat',
                                        'sort_order' => 3,
                                    ],
                                    [
                                        'name' => 'Entitats Bancaries',
                                        'slug' => 'entitats-bancaries',
                                        'description' => 'Documents bancaris',
                                        'sort_order' => 4,
                                    ],
                                ]
                            ],
                            [
                                'name' => 'Estatuts i Junta',
                                'slug' => 'estatuts-i-junta',
                                'description' => 'Estatuts i documents de la junta',
                                'sort_order' => 3,
                            ],
                            [
                                'name' => 'Qualificació d\'Entitat',
                                'slug' => 'qualificacio-entitat',
                                'description' => 'Documents de qualificació',
                                'sort_order' => 4,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Gestió de Cursos',
                        'slug' => 'gestio-cursos',
                        'description' => 'Documents de gestió acadèmica',
                        'sort_order' => 2,
                        'children' => [
                            [
                                'name' => 'Quadres i Gràfics',
                                'slug' => 'quadres-grafics',
                                'description' => 'Quadres estadístics i gràfics',
                                'sort_order' => 1,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Procediments',
                        'slug' => 'procediments',
                        'description' => 'Procediments i guies',
                        'sort_order' => 3,
                        'children' => [
                            [
                                'name' => 'Tasques de Secretaria',
                                'slug' => 'tasques-secretaria',
                                'description' => 'Procediments de tasques habituals',
                                'sort_order' => 1,
                            ],
                            [
                                'name' => 'Guia de Matriculació Online',
                                'slug' => 'guia-matriculacio-online',
                                'description' => 'Guia pas a pas de matriculació',
                                'sort_order' => 2,
                            ],
                        ]
                    ],
                    [
                        'name' => 'Anuaris',
                        'slug' => 'anuaris',
                        'description' => 'Anuaris anuals del campus',
                        'sort_order' => 4,
                    ],
                    [
                        'name' => 'Matriculacions',
                        'slug' => 'matriculacions',
                        'description' => 'Documents de matriculacions',
                        'sort_order' => 5,
                        'children' => [
                            [
                                'name' => 'Arxiu per Quadrimestres',
                                'slug' => 'arxiu-quadrimestres',
                                'description' => 'Matriculacions organitzades per quadrimestre',
                                'sort_order' => 1,
                            ],
                        ]
                    ],
                ]
            ]
        ];

        $this->createCategories($categories);
    }

    /**
     * Create categories recursively.
     */
    private function createCategories(array $categories, ?int $parentId = null): void
    {
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = DocumentCategory::create(array_merge($categoryData, [
                'parent_id' => $parentId,
            ]));

            if (!empty($children)) {
                $this->createCategories($children, $category->id);
            }
        }
    }
}
