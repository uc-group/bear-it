<?php

namespace App\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueFieldValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint|UniqueField $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueField) {
            throw new UnexpectedTypeException($constraint, UniqueField::class);
        }

        if (strlen($constraint->field) === 0) {
            throw new ConstraintDefinitionException("Field not specified.");
        }

        if (strlen($constraint->entityClass) === 0) {
            throw new ConstraintDefinitionException("Entity class not specified");
        }

        if ($value === null || strlen($value) === 0) {
            return;
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select(sprintf('e.%s', $constraint->field));
        $qb->setMaxResults(1);
        $qb->from($constraint->entityClass, 'e');
        $qb->andWhere(sprintf('e.%s = :value', $constraint->field));
        $qb->setParameter('value', $value);
        try {
            if ($qb->getQuery()->getSingleScalarResult()) {
                $this->addViolation($constraint);
            }
        } catch (NonUniqueResultException $exception) {
            $this->addViolation($constraint);
        } catch (NoResultException $exception) {
            return;
        }
    }

    /**
     * @param UniqueField $constraint
     */
    private function addViolation(UniqueField $constraint)
    {
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
